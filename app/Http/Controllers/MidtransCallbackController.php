<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MidtransCallbackController extends Controller
{
    public function handle(Request $request)
    {
        $serverKey = config('services.midtrans.server_key');
        $hashed = hash('sha512', $request->order_id.$request->status_code.$request->gross_amount.$serverKey);

        // 1. Mitigasi Timing Attack: Gunakan hash_equals
        if (! hash_equals($hashed, $request->signature_key ?? '')) {
            Log::warning('Midtrans Callback: Invalid Signature', [
                'received' => $request->signature_key,
                'calculated' => $hashed,
                'order_id' => $request->order_id,
            ]);

            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $transactionStatus = $request->transaction_status;
        $orderId = $request->order_id;
        $paymentType = $request->payment_type;

        // Extract Invoice IDs from custom_field1 or orderId
        $invoiceIds = [];
        if ($request->filled('custom_field1')) {
            $invoiceIds = explode(',', $request->custom_field1);
        } else {
            // Fallback to old format INV-{id}-{timestamp}
            $parts = explode('-', $orderId);
            if (count($parts) >= 2 && $parts[0] === 'INV') {
                $invoiceIds = [$parts[1]];
            }
        }

        if (empty($invoiceIds)) {
            Log::error('Midtrans Callback: No invoice IDs found', ['order_id' => $orderId]);

            return response()->json(['message' => 'Invalid order ID format'], 400);
        }

        try {
            DB::beginTransaction();

            // 2. Mitigasi Race Condition: Gunakan lockForUpdate (Pessimistic Locking)
            $invoices = Invoice::whereIn('id', $invoiceIds)->lockForUpdate()->get();

            if ($invoices->count() !== count($invoiceIds)) {
                DB::rollBack();
                Log::error('Midtrans Callback: Some invoices not found', [
                    'requested_ids' => $invoiceIds,
                    'found_count' => $invoices->count(),
                ]);

                return response()->json(['message' => 'Some invoices not found'], 404);
            }

            // 3. Mitigasi Parameter Tampering: Cek kesesuaian nominal bayar vs database
            // Kita harus menghitung ulang service fee untuk validasi gross_amount
            $totalInvoicesAmount = $invoices->sum('amount');

            // Get payment method slug for fee calculation
            // Midtrans payment_type is slightly different from our slugs in MidtransService
            $methodSlug = match ($paymentType) {
                'bank_transfer' => 'bca_va', // Default to flat fee
                'cstore' => 'dana', // Assuming similar fee for retail
                'gopay', 'qris' => 'qris',
                default => 'bca_va',
            };

            // If we have specific info from Midtrans about the bank
            if ($request->bank === 'bca') {
                $methodSlug = 'bca_va';
            }
            if ($request->bank === 'bri') {
                $methodSlug = 'bri_va';
            }
            if ($request->bank === 'mandiri') {
                $methodSlug = 'echannel';
            }

            $midtransService = app(MidtransService::class);
            $serviceFee = $midtransService->calculateFee((float) $totalInvoicesAmount, $methodSlug);
            $expectedTotal = (float) $totalInvoicesAmount + (float) $serviceFee;

            // Allow small epsilon for float comparison, but Midtrans usually sends exact string
            if (abs($expectedTotal - (float) $request->gross_amount) > 0.01) {
                DB::rollBack();
                Log::error('Midtrans Callback: Gross amount mismatch', [
                    'expected' => $expectedTotal,
                    'received' => $request->gross_amount,
                    'order_id' => $orderId,
                ]);

                return response()->json(['message' => 'Gross amount mismatch'], 400);
            }

            if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                foreach ($invoices as $invoice) {
                    if ($invoice->status === 'paid') {
                        continue;
                    }

                    // Generate Receipt Number
                    $prefix = 'SCH-'.date('Ym').'-';
                    $lastPayment = Payment::where('receipt_number', 'like', $prefix.'%')
                        ->orderBy('id', 'desc')
                        ->first();

                    $sequence = 1;
                    if ($lastPayment) {
                        $lastSequence = (int) substr($lastPayment->receipt_number, -4);
                        $sequence = $lastSequence + 1;
                    }
                    $receiptNumber = $prefix.str_pad($sequence, 4, '0', STR_PAD_LEFT);

                    // Create Payment record
                    Payment::create([
                        'invoice_id' => $invoice->id,
                        'amount' => $invoice->amount,
                        'method' => 'midtrans: '.$paymentType,
                        'paid_at' => now(),
                        'receipt_number' => $receiptNumber,
                        'recorded_by' => null, // Automated by system
                    ]);

                    // Update Invoice status
                    $invoice->update(['status' => 'paid']);
                }

                Log::info('Midtrans Callback: Payment success', ['order_id' => $orderId, 'invoices' => $invoiceIds]);
            } elseif ($transactionStatus == 'expire' || $transactionStatus == 'cancel' || $transactionStatus == 'deny') {
                foreach ($invoices as $invoice) {
                    if ($invoice->status !== 'paid') {
                        $invoice->update(['status' => 'unpaid']);
                    }
                }
                Log::info('Midtrans Callback: Payment failed/expired', ['order_id' => $orderId, 'status' => $transactionStatus]);
            }

            DB::commit();

            return response()->json(['message' => 'Success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Midtrans Callback: Error', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return response()->json(['message' => 'Internal server error'], 500);
        }
    }
}

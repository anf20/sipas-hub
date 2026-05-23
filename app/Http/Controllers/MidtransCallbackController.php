<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MidtransCallbackController extends Controller
{
    public function handle(Request $request)
    {
        $serverKey = config('services.midtrans.server_key');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        // 1. Mitigasi Timing Attack: Gunakan hash_equals
        if (!hash_equals($hashed, $request->signature_key ?? '')) {
            Log::warning('Midtrans Callback: Invalid Signature', [
                'received' => $request->signature_key,
                'calculated' => $hashed,
                'order_id' => $request->order_id
            ]);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $transactionStatus = $request->transaction_status;
        $orderId = $request->order_id; // Format: INV-{id}-{timestamp}
        $paymentType = $request->payment_type;

        // Extract Original Invoice ID
        $parts = explode('-', $orderId);
        if (count($parts) < 2) {
            return response()->json(['message' => 'Invalid order ID format'], 400);
        }
        $invoiceId = $parts[1];

        try {
            DB::beginTransaction();

            // 2. Mitigasi Race Condition: Gunakan lockForUpdate (Pessimistic Locking)
            // Ini memastikan jika ada 2 callback masuk bersamaan, mereka akan mengantre.
            $invoice = Invoice::where('id', $invoiceId)->lockForUpdate()->first();

            if (!$invoice) {
                DB::rollBack();
                Log::error('Midtrans Callback: Invoice not found', ['invoice_id' => $invoiceId]);
                return response()->json(['message' => 'Invoice not found'], 404);
            }

            // 3. Mitigasi Parameter Tampering: Cek kesesuaian nominal bayar vs database
            // Midtrans gross_amount bisa berupa desimal (e.g. 10000.00)
            if (abs((float) $invoice->amount - (float) $request->gross_amount) > 0.01) {
                DB::rollBack();
                Log::error('Midtrans Callback: Gross amount mismatch', [
                    'db_amount' => $invoice->amount,
                    'received_amount' => $request->gross_amount,
                    'order_id' => $orderId
                ]);
                return response()->json(['message' => 'Gross amount mismatch'], 400);
            }

            if ($invoice->status === 'paid') {
                DB::commit();
                return response()->json(['message' => 'Invoice already paid'], 200);
            }

            if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                // Generate Receipt Number
                $prefix = 'SCH-' . date('Ym') . '-';
                $lastPayment = Payment::where('receipt_number', 'like', $prefix . '%')
                    ->orderBy('id', 'desc')
                    ->first();

                $sequence = 1;
                if ($lastPayment) {
                    $lastSequence = (int) substr($lastPayment->receipt_number, -4);
                    $sequence = $lastSequence + 1;
                }
                $receiptNumber = $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);

                // Create Payment record
                Payment::create([
                    'invoice_id' => $invoice->id,
                    'amount' => $invoice->amount,
                    'method' => 'midtrans: ' . $paymentType,
                    'paid_at' => now(),
                    'receipt_number' => $receiptNumber,
                    'recorded_by' => null, // Automated by system
                ]);

                // Update Invoice status
                $invoice->update(['status' => 'paid']);
                
                Log::info('Midtrans Callback: Payment success', ['invoice_id' => $invoiceId]);
            } elseif ($transactionStatus == 'expire' || $transactionStatus == 'cancel' || $transactionStatus == 'deny') {
                $invoice->update(['status' => 'unpaid']);
                Log::info('Midtrans Callback: Payment failed/expired', ['invoice_id' => $invoiceId, 'status' => $transactionStatus]);
            }

            DB::commit();
            return response()->json(['message' => 'Success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Midtrans Callback: Error', ['message' => $e->getMessage()]);
            return response()->json(['message' => 'Internal server error'], 500);
        }
    }
}

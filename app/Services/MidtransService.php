<?php

namespace App\Services;

use App\Models\Invoice;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = config('services.midtrans.is_sanitized');
        Config::$is3ds = config('services.midtrans.is_3ds');
    }

    public function getSnapToken(Invoice $invoice): string
    {
        // Jika invoice sudah punya snap_token, kembalikan saja
        if ($invoice->snap_token) {
            return $invoice->snap_token;
        }

        $params = [
            'transaction_details' => [
                'order_id' => 'INV-'.$invoice->id.'-'.time(),
                'gross_amount' => (int) $invoice->amount,
            ],
            'customer_details' => [
                'first_name' => $invoice->student->name,
                'email' => $invoice->student->email ?? 'no-email@example.com',
            ],
            'item_details' => [
                [
                    'id' => $invoice->fee_type_id,
                    'price' => (int) $invoice->amount,
                    'quantity' => 1,
                    'name' => $invoice->feeType->name.' - '.$invoice->period_month.'/'.$invoice->period_year,
                ],
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        $invoice->update(['snap_token' => $snapToken]);

        return $snapToken;
    }

    /**
     * Generate Snap Token for multiple invoices
     */
    public function getBulkSnapToken($invoices, $user): string
    {
        $totalAmount = 0;
        $itemDetails = [];
        $invoiceIds = [];

        foreach ($invoices as $invoice) {
            $totalAmount += (int) $invoice->amount;
            $invoiceIds[] = $invoice->id;

            $itemDetails[] = [
                'id' => 'INV-'.$invoice->id,
                'price' => (int) $invoice->amount,
                'quantity' => 1,
                'name' => substr($invoice->student->name.' - '.$invoice->feeType->name, 0, 50),
            ];
        }

        $params = [
            'transaction_details' => [
                'order_id' => 'BULK-'.time().'-'.substr(md5(implode(',', $invoiceIds)), 0, 8),
                'gross_amount' => $totalAmount,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
            ],
            'item_details' => $itemDetails,
        ];

        return Snap::getSnapToken($params);
    }
}

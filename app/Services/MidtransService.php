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

    /**
     * Calculate service fee based on method and amount
     */
    public function calculateFee(float $amount, string $method): float
    {
        return match ($method) {
            'manual_transfer' => 0.0,
            'qris' => ceil($amount * 0.007), // 0.7%
            'dana' => ceil($amount * 0.015), // 1.5%
            'bca_va', 'bri_va', 'echannel' => 4500, // Flat VA
            default => 5000,
        };
    }

    public function getSnapToken(Invoice $invoice, string $method): string
    {
        $serviceFee = $this->calculateFee((float) $invoice->amount, $method);

        $params = [
            'transaction_details' => [
                'order_id' => 'INV-'.$invoice->id.'-'.time(),
                'gross_amount' => (int) $invoice->amount + (int) $serviceFee,
            ],
            'customer_details' => [
                'first_name' => $invoice->student->name,
                'email' => $invoice->student->email ?? 'no-email@example.com',
            ],
            'item_details' => [
                [
                    'id' => 'FEETYPE-'.$invoice->fee_type_id,
                    'price' => (int) $invoice->amount,
                    'quantity' => 1,
                    'name' => substr($invoice->feeType->name.' - '.$invoice->period_month.'/'.$invoice->period_year, 0, 50),
                ],
                [
                    'id' => 'SERVICE-FEE',
                    'price' => (int) $serviceFee,
                    'quantity' => 1,
                    'name' => 'Biaya Layanan Online',
                ],
            ],
            'enabled_payments' => [$method],
            'custom_field1' => (string) $invoice->id,
        ];

        return Snap::getSnapToken($params);
    }

    /**
     * Generate Snap Token for multiple invoices
     */
    public function getBulkSnapToken($invoices, $user, string $method): string
    {
        $totalInvoicesAmount = 0;
        $itemDetails = [];
        $invoiceIds = [];

        foreach ($invoices as $invoice) {
            $totalInvoicesAmount += (float) $invoice->amount;
            $invoiceIds[] = $invoice->id;

            $itemDetails[] = [
                'id' => 'INV-'.$invoice->id,
                'price' => (int) $invoice->amount,
                'quantity' => 1,
                'name' => substr($invoice->student->name.' - '.$invoice->feeType->name, 0, 50),
            ];
        }

        $serviceFee = $this->calculateFee($totalInvoicesAmount, $method);

        $itemDetails[] = [
            'id' => 'SERVICE-FEE',
            'price' => (int) $serviceFee,
            'quantity' => 1,
            'name' => 'Biaya Layanan Online',
        ];

        $params = [
            'transaction_details' => [
                'order_id' => 'BULK-'.time().'-'.substr(md5(implode(',', $invoiceIds)), 0, 8),
                'gross_amount' => (int) $totalInvoicesAmount + (int) $serviceFee,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
            ],
            'item_details' => $itemDetails,
            'enabled_payments' => [$method],
            'custom_field1' => implode(',', $invoiceIds),
        ];

        return Snap::getSnapToken($params);
    }
}

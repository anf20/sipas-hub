<?php

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;

uses(RefreshDatabase::class);

beforeEach(function () {
    Config::set('services.midtrans.server_key', 'test-server-key');
});

test('it handles callback with service fee correctly', function () {
    $invoice = Invoice::factory()->create([
        'amount' => 100000,
        'status' => 'unpaid',
    ]);

    $serviceFee = 4500; // Flat VA fee (match logic in MidtransCallbackController)
    $totalAmount = 100000 + $serviceFee;

    $orderId = 'INV-'.$invoice->id.'-'.time();
    $statusCode = '200';
    $grossAmount = (string) $totalAmount;
    $serverKey = 'test-server-key';

    $signatureKey = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);

    $payload = [
        'order_id' => $orderId,
        'status_code' => $statusCode,
        'gross_amount' => $grossAmount,
        'signature_key' => $signatureKey,
        'transaction_status' => 'settlement',
        'payment_type' => 'bank_transfer',
        'custom_field1' => (string) $invoice->id,
    ];

    $response = $this->postJson(route('midtrans.callback'), $payload);

    $response->assertSuccessful();
    $response->assertJson(['message' => 'Success']);

    $invoice->refresh();
    expect($invoice->status)->toBe('paid');

    $payment = Payment::where('invoice_id', $invoice->id)->first();
    expect($payment)->not->toBeNull();
    expect((float) $payment->amount)->toBe(100000.0);
});

test('it handles bulk payment callback correctly', function () {
    $invoice1 = Invoice::factory()->create(['amount' => 100000, 'status' => 'unpaid']);
    $invoice2 = Invoice::factory()->create(['amount' => 200000, 'status' => 'unpaid']);

    $totalInvoicesAmount = 100000 + 200000;
    $serviceFee = 4500; // Flat VA fee
    $totalAmount = $totalInvoicesAmount + $serviceFee;

    $orderId = 'BULK-'.time().'-hash';
    $statusCode = '200';
    $grossAmount = (string) $totalAmount;
    $serverKey = 'test-server-key';

    $signatureKey = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);

    $payload = [
        'order_id' => $orderId,
        'status_code' => $statusCode,
        'gross_amount' => $grossAmount,
        'signature_key' => $signatureKey,
        'transaction_status' => 'settlement',
        'payment_type' => 'bank_transfer',
        'custom_field1' => $invoice1->id.','.$invoice2->id,
    ];

    $response = $this->postJson(route('midtrans.callback'), $payload);

    $response->assertSuccessful();
    $response->assertJson(['message' => 'Success']);

    $invoice1->refresh();
    $invoice2->refresh();
    expect($invoice1->status)->toBe('paid');
    expect($invoice2->status)->toBe('paid');

    expect(Payment::where('invoice_id', $invoice1->id)->exists())->toBeTrue();
    expect(Payment::where('invoice_id', $invoice2->id)->exists())->toBeTrue();
});

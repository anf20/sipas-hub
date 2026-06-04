<?php

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;

uses(RefreshDatabase::class);

beforeEach(function () {
    Config::set('services.midtrans.server_key', 'test-server-key');
});

test('it handles successful midtrans callback', function () {
    $invoice = Invoice::factory()->create([
        'amount' => 100000,
        'status' => 'unpaid',
    ]);

    $orderId = 'INV-'.$invoice->id.'-'.time();
    $statusCode = '200';
    $grossAmount = '100000.00';
    $serverKey = 'test-server-key';

    $signatureKey = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);

    $payload = [
        'order_id' => $orderId,
        'status_code' => $statusCode,
        'gross_amount' => $grossAmount,
        'signature_key' => $signatureKey,
        'transaction_status' => 'settlement',
        'payment_type' => 'bank_transfer',
    ];

    $response = $this->postJson(route('midtrans.callback'), $payload);

    $response->assertSuccessful();
    $response->assertJson(['message' => 'Success']);

    $invoice->refresh();
    expect($invoice->status)->toBe('paid');

    $payment = Payment::where('invoice_id', $invoice->id)->first();
    expect($payment)->not->toBeNull();
    expect((float) $payment->amount)->toBe(100000.0);
    expect($payment->method)->toBe('midtrans: bank_transfer');
});

test('it rejects invalid signature', function () {
    $invoice = Invoice::factory()->create([
        'amount' => 100000,
        'status' => 'unpaid',
    ]);

    $payload = [
        'order_id' => 'INV-'.$invoice->id.'-12345',
        'status_code' => '200',
        'gross_amount' => '100000.00',
        'signature_key' => 'wrong-signature',
        'transaction_status' => 'settlement',
    ];

    $response = $this->postJson(route('midtrans.callback'), $payload);

    $response->assertForbidden();
    $response->assertJson(['message' => 'Invalid signature']);

    $invoice->refresh();
    expect($invoice->status)->toBe('unpaid');
});

test('it handles failed transaction status', function () {
    $invoice = Invoice::factory()->create([
        'amount' => 100000,
        'status' => 'unpaid',
    ]);

    $orderId = 'INV-'.$invoice->id.'-'.time();
    $statusCode = '200';
    $grossAmount = '100000.00';
    $serverKey = 'test-server-key';

    $signatureKey = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);

    $payload = [
        'order_id' => $orderId,
        'status_code' => $statusCode,
        'gross_amount' => $grossAmount,
        'signature_key' => $signatureKey,
        'transaction_status' => 'expire',
    ];

    $response = $this->postJson(route('midtrans.callback'), $payload);

    $response->assertSuccessful();

    $invoice->refresh();
    expect($invoice->status)->toBe('unpaid');

    expect(Payment::where('invoice_id', $invoice->id)->exists())->toBeFalse();
});

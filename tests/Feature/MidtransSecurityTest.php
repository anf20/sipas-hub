<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Student;
use App\Models\FeeType;
use App\Models\AcademicYear;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MidtransSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected $serverKey;
    protected $invoice;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->serverKey = config('services.midtrans.server_key');

        $year = AcademicYear::create([
            'name' => '2025/2026',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
            'is_active' => true
        ]);

        $fee = FeeType::create([
            'name' => 'SPP Test',
            'category' => 'SPP',
            'default_amount' => 100000,
            'is_recurring' => true,
            'is_active' => true
        ]);

        $student = Student::factory()->create();

        $this->invoice = Invoice::create([
            'student_id' => $student->id,
            'fee_type_id' => $fee->id,
            'amount' => 100000,
            'due_date' => now()->addDays(7),
            'status' => 'unpaid',
            'period_month' => 5,
            'period_year' => 2026
        ]);
    }

    /** @test */
    public function it_rejects_callback_with_invalid_signature()
    {
        $payload = [
            'order_id' => 'INV-' . $this->invoice->id . '-123',
            'status_code' => '200',
            'gross_amount' => '100000.00',
            'transaction_status' => 'settlement',
            'signature_key' => 'palsu-banget-signatures-nya'
        ];

        $response = $this->postJson('/midtrans/callback', $payload);

        $response->assertStatus(403);
        $this->assertEquals('unpaid', $this->invoice->fresh()->status);
    }

    /** @test */
    public function it_rejects_callback_with_mismatched_amount_parameter_tampering()
    {
        $orderId = 'INV-' . $this->invoice->id . '-123';
        $statusCode = '200';
        $fakeAmount = '5000.00'; // Hacker mencoba bayar Rp 5.000 untuk tagihan Rp 100.000
        
        $signature = hash("sha512", $orderId . $statusCode . $fakeAmount . $this->serverKey);

        $payload = [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $fakeAmount,
            'transaction_status' => 'settlement',
            'signature_key' => $signature
        ];

        $response = $this->postJson('/midtrans/callback', $payload);

        $response->assertStatus(400); // Bad Request
        $this->assertEquals('unpaid', $this->invoice->fresh()->status);
    }

    /** @test */
    public function it_accepts_valid_callback_and_updates_status()
    {
        $orderId = 'INV-' . $this->invoice->id . '-123';
        $statusCode = '200';
        $amount = '100000.00';
        
        $signature = hash("sha512", $orderId . $statusCode . $amount . $this->serverKey);

        $payload = [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $amount,
            'transaction_status' => 'settlement',
            'payment_type' => 'bank_transfer',
            'signature_key' => $signature
        ];

        $response = $this->postJson('/midtrans/callback', $payload);

        $response->assertStatus(200);
        $this->assertEquals('paid', $this->invoice->fresh()->status);
        $this->assertDatabaseHas('payments', ['invoice_id' => $this->invoice->id]);
    }
}

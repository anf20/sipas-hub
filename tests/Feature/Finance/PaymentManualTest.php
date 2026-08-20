<?php

use App\Livewire\Pages\Finance\PaymentManual;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\TestDatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(TestDatabaseSeeder::class);
    $this->admin = User::where('email', 'admin@test.com')->first();
    $this->student = Student::first();
    $this->invoice = Invoice::where('student_id', $this->student->id)->where('status', 'unpaid')->first();
});

test('admin can process manual payment with cash method without uploading proof file', function () {
    Livewire::actingAs($this->admin)
        ->test(PaymentManual::class)
        ->set('selectedStudentId', $this->student->id)
        ->set('selectedInvoiceId', $this->invoice->id)
        ->set('paymentMethod', 'cash')
        ->set('paymentAmount', $this->invoice->amount)
        ->call('processPayment')
        ->assertHasNoErrors();

    // Verify invoice is paid
    expect($this->invoice->fresh()->status)->toBe('paid');

    // Verify payment record is created and successful
    $payment = Payment::where('invoice_id', $this->invoice->id)->first();
    expect($payment)->not->toBeNull()
        ->and($payment->status)->toBe('success')
        ->and($payment->method)->toBe('cash')
        ->and($payment->proof_file)->toBeNull();
});

test('admin cannot process manual payment with transfer method without uploading proof file', function () {
    Livewire::actingAs($this->admin)
        ->test(PaymentManual::class)
        ->set('selectedStudentId', $this->student->id)
        ->set('selectedInvoiceId', $this->invoice->id)
        ->set('paymentMethod', 'transfer')
        ->set('paymentAmount', $this->invoice->amount)
        ->call('processPayment')
        ->assertHasErrors(['proofFile' => 'required']);

    // Verify invoice is still unpaid
    expect($this->invoice->fresh()->status)->toBe('unpaid');

    // Verify no payment record is created
    expect(Payment::where('invoice_id', $this->invoice->id)->exists())->toBeFalse();
});

test('admin can process manual payment with transfer method and uploading proof file', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('bukti_tf.jpg');

    Livewire::actingAs($this->admin)
        ->test(PaymentManual::class)
        ->set('selectedStudentId', $this->student->id)
        ->set('selectedInvoiceId', $this->invoice->id)
        ->set('paymentMethod', 'transfer')
        ->set('paymentAmount', $this->invoice->amount)
        ->set('proofFile', $file)
        ->call('processPayment')
        ->assertHasNoErrors();

    // Verify invoice is paid
    expect($this->invoice->fresh()->status)->toBe('paid');

    // Verify payment record is created and successful with proof_file
    $payment = Payment::where('invoice_id', $this->invoice->id)->first();
    expect($payment)->not->toBeNull()
        ->and($payment->status)->toBe('success')
        ->and($payment->method)->toBe('transfer')
        ->and($payment->proof_file)->not->toBeNull()
        ->and($payment->proof_file)->toEndWith('.webp');

    // Verify file exists
    Storage::disk('public')->assertExists($payment->proof_file);
});

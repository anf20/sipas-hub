<?php

use App\Jobs\SendGeneralWhatsappNotification;
use App\Livewire\Pages\Finance\PaymentVerification;
use App\Livewire\Pages\Parent\InvoiceDetail;
use App\Livewire\Pages\Parent\Invoices as ParentInvoices;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\TestDatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(TestDatabaseSeeder::class);
    $this->parent = User::where('email', 'wali@test.com')->first();
    $this->admin = User::where('email', 'admin@test.com')->first();

    // Set parent phone number
    $this->parent->update(['phone' => '6281234567890']);

    $this->student = Student::where('parent_user_id', $this->parent->id)->first();
    $this->invoice = Invoice::where('student_id', $this->student->id)->where('status', 'unpaid')->first();
});

test('parent can upload manual payment proof on invoice detail page', function () {
    Storage::fake('public');
    Queue::fake();

    $file = UploadedFile::fake()->image('bukti_transfer.jpg');

    Livewire::actingAs($this->parent)
        ->test(InvoiceDetail::class, ['invoice' => $this->invoice])
        ->set('paymentMethod', 'manual_transfer')
        ->set('proofFile', $file)
        ->call('pay')
        ->assertRedirect(); // redirects to whatsapp

    // Verify invoice is pending
    expect($this->invoice->fresh()->status)->toBe('pending');

    // Verify payment record is created and pending
    $payment = Payment::where('invoice_id', $this->invoice->id)->first();
    expect($payment)->not->toBeNull()
        ->and($payment->status)->toBe('pending')
        ->and($payment->method)->toBe('transfer')
        ->and($payment->proof_file)->not->toBeNull()
        ->and($payment->proof_file)->toEndWith('.webp');

    // Verify file is stored
    Storage::disk('public')->assertExists($payment->proof_file);
});

test('parent can upload manual payment proof on bulk invoices page', function () {
    Storage::fake('public');
    Queue::fake();

    $file = UploadedFile::fake()->image('bukti_bulk.png');

    Livewire::actingAs($this->parent)
        ->test(ParentInvoices::class)
        ->set('selectedInvoices', [(string) $this->invoice->id])
        ->set('paymentMethod', 'manual_transfer')
        ->set('proofFile', $file)
        ->call('paySelected')
        ->assertRedirect();

    // Verify invoice is pending
    expect($this->invoice->fresh()->status)->toBe('pending');

    $payment = Payment::where('invoice_id', $this->invoice->id)->first();
    expect($payment)->not->toBeNull()
        ->and($payment->status)->toBe('pending')
        ->and($payment->proof_file)->toEndWith('.webp');
});

test('admin can approve pending payment proof', function () {
    Storage::fake('public');
    Queue::fake();

    // Create a pending payment
    $payment = Payment::create([
        'invoice_id' => $this->invoice->id,
        'amount' => $this->invoice->amount,
        'method' => 'transfer',
        'status' => 'pending',
        'proof_file' => 'payment-proofs/dummy.jpg',
        'paid_at' => now(),
        'receipt_number' => 'SCH-PEND-202608-0001',
    ]);
    $this->invoice->update(['status' => 'pending']);

    Livewire::actingAs($this->admin)
        ->test(PaymentVerification::class)
        ->call('approve', $payment->id)
        ->assertHasNoErrors();

    // Verify statuses
    expect($payment->fresh()->status)->toBe('success');
    expect($payment->fresh()->receipt_number)->toStartWith('SCH-202608-');
    expect($payment->fresh()->recorded_by)->toBe($this->admin->id);
    expect($this->invoice->fresh()->status)->toBe('paid');

    // Verify WhatsApp notification job is dispatched
    Queue::assertPushed(SendGeneralWhatsappNotification::class);
});

test('admin can reject pending payment proof with a reason', function () {
    Storage::fake('public');
    Queue::fake();

    // Create a pending payment
    $payment = Payment::create([
        'invoice_id' => $this->invoice->id,
        'amount' => $this->invoice->amount,
        'method' => 'transfer',
        'status' => 'pending',
        'proof_file' => 'payment-proofs/dummy.jpg',
        'paid_at' => now(),
        'receipt_number' => 'SCH-PEND-202608-0001',
    ]);
    $this->invoice->update(['status' => 'pending']);

    Livewire::actingAs($this->admin)
        ->test(PaymentVerification::class)
        ->set('selectedPaymentId', $payment->id)
        ->set('rejectionReason', 'Bukti bayar tidak terbaca.')
        ->call('reject')
        ->assertHasNoErrors();

    // Verify statuses
    expect($payment->fresh()->status)->toBe('rejected');
    expect($this->invoice->fresh()->status)->toBe('unpaid');

    // Verify WhatsApp notification job is dispatched
    Queue::assertPushed(SendGeneralWhatsappNotification::class);
});

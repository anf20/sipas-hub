<?php

use App\Livewire\Pages\Admin\RecoveryCenter;
use App\Models\AcademicYear;
use App\Models\FeeType;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);

    // Super Admin User
    $this->admin = User::factory()->create();
    $this->admin->assignRole('Super Admin');

    // Orang Tua User (Unauthorized)
    $this->parent = User::factory()->create();
    $this->parent->assignRole('Orang Tua');

    // Setup basic academic data
    $this->academicYear = AcademicYear::create([
        'name' => '2024/2025',
        'start_date' => '2024-07-01',
        'end_date' => '2025-06-30',
        'is_active' => true,
    ]);

    $this->schoolClass = SchoolClass::create([
        'name' => 'X-IPA-1',
        'grade' => '10',
        'academic_year_id' => $this->academicYear->id,
    ]);

    $this->feeType = FeeType::create([
        'name' => 'SPP',
        'category' => 'SPP',
        'default_amount' => 500000,
        'is_recurring' => true,
        'recurrence' => 'bulanan',
        'is_active' => true,
    ]);
});

test('guest cannot access recovery page', function () {
    $this->get(route('management.recovery'))
        ->assertRedirect(route('login'));
});

test('parent cannot access recovery page', function () {
    $this->actingAs($this->parent)
        ->get(route('management.recovery'))
        ->assertForbidden();
});

test('super admin can access recovery page', function () {
    $this->actingAs($this->admin)
        ->get(route('management.recovery'))
        ->assertSuccessful();
});

test('can restore soft deleted student', function () {
    $student = Student::create([
        'nis' => '12345',
        'name' => 'Student to delete',
        'school_class_id' => $this->schoolClass->id,
        'gender' => 'L',
        'entry_year' => '2024',
        'status' => 'aktif',
    ]);

    $student->delete();
    $this->assertSoftDeleted('students', ['id' => $student->id]);

    Livewire::actingAs($this->admin)
        ->test(RecoveryCenter::class)
        ->call('restoreStudent', $student->id);

    $this->assertDatabaseHas('students', [
        'id' => $student->id,
        'deleted_at' => null,
    ]);
});

test('can force delete soft deleted student', function () {
    $student = Student::create([
        'nis' => '123456',
        'name' => 'Student to delete forever',
        'school_class_id' => $this->schoolClass->id,
        'gender' => 'L',
        'entry_year' => '2024',
        'status' => 'aktif',
    ]);

    $student->delete();
    $this->assertSoftDeleted('students', ['id' => $student->id]);

    Livewire::actingAs($this->admin)
        ->test(RecoveryCenter::class)
        ->call('forceDeleteStudent', $student->id);

    $this->assertDatabaseMissing('students', ['id' => $student->id]);
});

test('can restore soft deleted invoice', function () {
    $student = Student::create([
        'nis' => '123457',
        'name' => 'Student for invoice',
        'school_class_id' => $this->schoolClass->id,
        'gender' => 'L',
        'entry_year' => '2024',
        'status' => 'aktif',
    ]);

    $invoice = Invoice::create([
        'student_id' => $student->id,
        'fee_type_id' => $this->feeType->id,
        'amount' => 500000,
        'due_date' => now()->addMonth(),
        'period_month' => 6,
        'period_year' => 2026,
        'status' => 'unpaid',
        'generated_by' => $this->admin->id,
    ]);

    $invoice->delete();
    $this->assertSoftDeleted('invoices', ['id' => $invoice->id]);

    Livewire::actingAs($this->admin)
        ->test(RecoveryCenter::class)
        ->call('restoreInvoice', $invoice->id);

    $this->assertDatabaseHas('invoices', [
        'id' => $invoice->id,
        'deleted_at' => null,
    ]);
});

test('can force delete soft deleted invoice', function () {
    $student = Student::create([
        'nis' => '123458',
        'name' => 'Student for invoice delete',
        'school_class_id' => $this->schoolClass->id,
        'gender' => 'L',
        'entry_year' => '2024',
        'status' => 'aktif',
    ]);

    $invoice = Invoice::create([
        'student_id' => $student->id,
        'fee_type_id' => $this->feeType->id,
        'amount' => 500000,
        'due_date' => now()->addMonth(),
        'period_month' => 6,
        'period_year' => 2026,
        'status' => 'unpaid',
        'generated_by' => $this->admin->id,
    ]);

    $invoice->delete();
    $this->assertSoftDeleted('invoices', ['id' => $invoice->id]);

    Livewire::actingAs($this->admin)
        ->test(RecoveryCenter::class)
        ->call('forceDeleteInvoice', $invoice->id);

    $this->assertDatabaseMissing('invoices', ['id' => $invoice->id]);
});

test('can restore soft deleted payment', function () {
    $student = Student::create([
        'nis' => '123459',
        'name' => 'Student for payment',
        'school_class_id' => $this->schoolClass->id,
        'gender' => 'L',
        'entry_year' => '2024',
        'status' => 'aktif',
    ]);

    $invoice = Invoice::create([
        'student_id' => $student->id,
        'fee_type_id' => $this->feeType->id,
        'amount' => 500000,
        'due_date' => now()->addMonth(),
        'period_month' => 6,
        'period_year' => 2026,
        'status' => 'paid',
        'generated_by' => $this->admin->id,
    ]);

    $payment = Payment::create([
        'invoice_id' => $invoice->id,
        'amount' => 500000,
        'method' => 'cash',
        'status' => 'success',
        'paid_at' => now(),
        'receipt_number' => 'SCH-202606-000001',
        'recorded_by' => $this->admin->id,
    ]);

    $payment->delete();
    $this->assertSoftDeleted('payments', ['id' => $payment->id]);

    Livewire::actingAs($this->admin)
        ->test(RecoveryCenter::class)
        ->call('restorePayment', $payment->id);

    $this->assertDatabaseHas('payments', [
        'id' => $payment->id,
        'deleted_at' => null,
    ]);
});

test('can force delete soft deleted payment', function () {
    $student = Student::create([
        'nis' => '123460',
        'name' => 'Student for payment delete',
        'school_class_id' => $this->schoolClass->id,
        'gender' => 'L',
        'entry_year' => '2024',
        'status' => 'aktif',
    ]);

    $invoice = Invoice::create([
        'student_id' => $student->id,
        'fee_type_id' => $this->feeType->id,
        'amount' => 500000,
        'due_date' => now()->addMonth(),
        'period_month' => 6,
        'period_year' => 2026,
        'status' => 'paid',
        'generated_by' => $this->admin->id,
    ]);

    $payment = Payment::create([
        'invoice_id' => $invoice->id,
        'amount' => 500000,
        'method' => 'cash',
        'status' => 'success',
        'paid_at' => now(),
        'receipt_number' => 'SCH-202606-000002',
        'recorded_by' => $this->admin->id,
    ]);

    $payment->delete();
    $this->assertSoftDeleted('payments', ['id' => $payment->id]);

    Livewire::actingAs($this->admin)
        ->test(RecoveryCenter::class)
        ->call('forceDeletePayment', $payment->id);

    $this->assertDatabaseMissing('payments', ['id' => $payment->id]);
});

<?php

use App\Jobs\GenerateInvoices;
use App\Jobs\GenerateYearlyInvoiceJob;
use App\Livewire\Pages\Finance\SppIndex;
use App\Models\AcademicYear;
use App\Models\FeeType;
use App\Models\Invoice;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);
    $this->user = User::factory()->create(['email_verified_at' => now()]);
    $this->user->assignRole('Super Admin');

    $year = AcademicYear::create([
        'name' => '2024/2025',
        'start_date' => '2024-07-01',
        'end_date' => '2025-06-30',
        'is_active' => true,
    ]);

    $this->class = SchoolClass::create([
        'name' => 'X IPA 1',
        'grade' => '10',
        'academic_year_id' => $year->id,
    ]);

    $this->feeType = FeeType::create([
        'name' => 'Biaya Tahunan',
        'category' => 'SPP',
        'default_amount' => 200000,
        'is_recurring' => true,
        'recurrence' => 'bulanan',
        'is_active' => true,
    ]);

    $this->students = Student::factory()->count(5)->create([
        'school_class_id' => $this->class->id,
        'status' => 'aktif',
    ]);
});

it('dispatches the generation job via finance hub', function () {
    Queue::fake();

    Livewire::actingAs($this->user)
        ->test(SppIndex::class)
        ->set('year', 2025)
        ->set('default_amount', 200000)
        ->call('generateSpp')
        ->assertHasNoErrors();

    Queue::assertPushed(GenerateYearlyInvoiceJob::class);
});

it('generates invoices for all active students', function () {
    $job = new GenerateInvoices(
        $this->feeType->id,
        5,
        2025,
        '2025-05-10',
        ['type' => 'all', 'value' => null],
        $this->user->id
    );

    $job->handle();

    $this->assertEquals(5, Invoice::count());
    $this->assertDatabaseHas('invoices', [
        'student_id' => $this->students->first()->id,
        'amount' => 200000,
        'period_month' => 5,
        'period_year' => 2025,
    ]);
});

it('does not generate duplicate invoices', function () {
    // Generate once
    $job = new GenerateInvoices(
        $this->feeType->id,
        5,
        2025,
        '2025-05-10',
        ['type' => 'all', 'value' => null],
        $this->user->id
    );
    $job->handle();
    $this->assertEquals(5, Invoice::count());

    // Generate again for same period
    $job->handle();
    $this->assertEquals(5, Invoice::count()); // Should still be 5
});

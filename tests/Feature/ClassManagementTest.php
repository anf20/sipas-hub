<?php

use App\Livewire\Pages\Academic\AcademicYearCreate;
use App\Livewire\Pages\Academic\ClassCreate;
use App\Models\AcademicYear;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);
    $this->user = User::factory()->create();
});

it('can create an academic year', function () {
    Livewire::actingAs($this->user)
        ->test(AcademicYearCreate::class)
        ->set('name', '2024/2025')
        ->set('start_date', '2024-07-01')
        ->set('end_date', '2025-06-30')
        ->set('is_active', true)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('academic.years.index'));

    $this->assertDatabaseHas('academic_years', [
        'name' => '2024/2025',
        'is_active' => 1,
    ]);
});

it('can create a class', function () {
    $year = AcademicYear::create([
        'name' => '2024/2025',
        'start_date' => '2024-07-01',
        'end_date' => '2025-06-30',
        'is_active' => true,
    ]);

    Livewire::actingAs($this->user)
        ->test(ClassCreate::class)
        ->set('name', 'X IPA 1')
        ->set('grade', '10')
        ->set('academic_year_id', $year->id)
        ->set('capacity', 32)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('academic.classes.index'));

    $this->assertDatabaseHas('school_classes', [
        'name' => 'X IPA 1',
        'academic_year_id' => $year->id,
        'capacity' => 32,
    ]);
});

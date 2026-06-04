<?php

use App\Livewire\Pages\Academic\ClassShow;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);
    $this->user = User::factory()->create();
    $this->user->assignRole('Super Admin');

    $this->academicYear = AcademicYear::factory()->create(['is_active' => true]);
    $this->schoolClass = SchoolClass::factory()->create([
        'academic_year_id' => $this->academicYear->id,
        'grade' => '7',
        'name' => 'Kelas 7 Test',
    ]);
});

test('it can render class show page', function () {
    $this->actingAs($this->user)
        ->get(route('academic.classes.show', $this->schoolClass))
        ->assertOk()
        ->assertSee('Kelas 7 Test');
});

test('it displays class information', function () {
    Livewire::actingAs($this->user)
        ->test(ClassShow::class, ['schoolClass' => $this->schoolClass])
        ->assertSee($this->schoolClass->name)
        ->assertSee($this->academicYear->name)
        ->assertSee($this->schoolClass->grade);
});

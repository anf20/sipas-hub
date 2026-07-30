<?php

use App\Livewire\Pages\Academic\StudentCreate;
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

    $this->academicYear = AcademicYear::create([
        'name' => '2024/2025',
        'start_date' => '2024-07-01',
        'end_date' => '2025-06-30',
        'is_active' => true,
    ]);
    $this->class = SchoolClass::create([
        'name' => 'X-IPA-1',
        'grade' => '10',
        'academic_year_id' => $this->academicYear->id,
    ]);
});

it('can render the student create page', function () {
    $this->actingAs($this->user)
        ->get(route('academic.students.create'))
        ->assertOk()
        ->assertSee('Tambah Siswa');
});

it('can create a new student', function () {
    Livewire::actingAs($this->user)
        ->test(StudentCreate::class)
        ->set('name', 'Budi Santoso')
        ->set('school_class_id', $this->class->id)
        ->set('gender', 'L')
        ->set('entry_year', '2024')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('academic.students.index'));

    $this->assertDatabaseHas('students', [
        'nis' => '240001',
        'name' => 'Budi Santoso',
        'school_class_id' => $this->class->id,
    ]);
});

it('validates required fields', function () {
    Livewire::actingAs($this->user)
        ->test(StudentCreate::class)
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required', 'school_class_id' => 'required']);
});

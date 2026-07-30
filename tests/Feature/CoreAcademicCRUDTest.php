<?php

use App\Livewire\Pages\Academic\AcademicYearEdit;
use App\Livewire\Pages\Academic\AcademicYearIndex;
use App\Livewire\Pages\Academic\ClassEdit;
use App\Livewire\Pages\Academic\ClassIndex;
use App\Livewire\Pages\Academic\StudentEdit;
use App\Livewire\Pages\Academic\StudentIndex;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);
    $this->user = User::factory()->create();
    $this->user->assignRole('Super Admin');

    $this->year = AcademicYear::create([
        'name' => '2024/2025',
        'start_date' => '2024-07-01',
        'end_date' => '2025-06-30',
        'is_active' => true,
    ]);

    $this->class = SchoolClass::create([
        'name' => 'X IPA 1',
        'grade' => '10',
        'academic_year_id' => $this->year->id,
        'capacity' => 30,
    ]);

    $this->student = Student::create([
        'nis' => '12345678',
        'name' => 'Original Student',
        'school_class_id' => $this->class->id,
        'gender' => 'L',
        'entry_year' => '2024',
        'status' => 'aktif',
    ]);
});

/* --- Academic Year --- */

it('can update academic year', function () {
    Livewire::actingAs($this->user)
        ->test(AcademicYearEdit::class, ['academicYear' => $this->year])
        ->set('name', '2024/2025 Updated')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('academic.years.index'));

    expect($this->year->fresh()->name)->toBe('2024/2025 Updated');
});

it('can delete academic year without classes', function () {
    $newYear = AcademicYear::create([
        'name' => 'Empty Year',
        'start_date' => '2026-07-01',
        'end_date' => '2027-06-30',
    ]);

    Livewire::actingAs($this->user)
        ->test(AcademicYearIndex::class)
        ->call('deleteYear', $newYear->id);

    $this->assertDatabaseMissing('academic_years', ['id' => $newYear->id]);
});

/* --- Class --- */

it('can update class', function () {
    Livewire::actingAs($this->user)
        ->test(ClassEdit::class, ['schoolClass' => $this->class])
        ->set('name', 'X IPA 1 Edited')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('academic.classes.index'));

    expect($this->class->fresh()->name)->toBe('X IPA 1 Edited');
});

it('can delete class without students', function () {
    $emptyClass = SchoolClass::create([
        'name' => 'Empty Class',
        'grade' => '10',
        'academic_year_id' => $this->year->id,
    ]);

    Livewire::actingAs($this->user)
        ->test(ClassIndex::class)
        ->call('deleteClass', $emptyClass->id);

    $this->assertDatabaseMissing('school_classes', ['id' => $emptyClass->id]);
});

/* --- Student --- */

it('can update student', function () {
    Livewire::actingAs($this->user)
        ->test(StudentEdit::class, ['student' => $this->student])
        ->set('name', 'Updated Student Name')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('academic.students.index'));

    expect($this->student->fresh()->name)->toBe('Updated Student Name');
});

it('can soft delete student', function () {
    Livewire::actingAs($this->user)
        ->test(StudentIndex::class)
        ->call('deleteStudent', $this->student->id);

    $this->assertSoftDeleted('students', ['id' => $this->student->id]);
});

<?php

use App\Livewire\Pages\Academic\AcademicHub;
use App\Livewire\Pages\Academic\AcademicYearCreate;
use App\Livewire\Pages\Academic\AcademicYearEdit;
use App\Livewire\Pages\Academic\ClassCreate;
use App\Livewire\Pages\Academic\ClassEdit;
use App\Livewire\Pages\Academic\ClassShow;
use App\Livewire\Pages\Academic\StudentCreate;
use App\Livewire\Pages\Academic\StudentEdit;
use App\Livewire\Pages\Academic\StudentShow;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('academic')->name('academic.')->group(function () {
    Route::get('/', AcademicHub::class)->name('hub');

    // Keep original index routes for test compatibility, but they can redirect to hub
    Route::get('students', fn () => redirect()->route('academic.hub', ['tab' => 'students']))->name('students.index');
    Route::get('classes', fn () => redirect()->route('academic.hub', ['tab' => 'classes']))->name('classes.index');
    Route::get('years', fn () => redirect()->route('academic.hub', ['tab' => 'years']))->name('years.index');

    Route::get('students/create', StudentCreate::class)->name('students.create');
    Route::get('students/{student}', StudentShow::class)->name('students.show');
    Route::get('students/{student}/edit', StudentEdit::class)->name('students.edit');

    Route::get('classes/create', ClassCreate::class)->name('classes.create');
    Route::get('classes/{schoolClass}', ClassShow::class)->name('classes.show');
    Route::get('classes/{schoolClass}/edit', ClassEdit::class)->name('classes.edit');

    Route::get('years/create', AcademicYearCreate::class)->name('years.create');
    Route::get('years/{academicYear}/edit', AcademicYearEdit::class)->name('years.edit');
});

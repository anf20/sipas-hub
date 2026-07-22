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
    Route::get('/', \App\Livewire\Pages\Academic\AcademicDashboard::class)->name('dashboard');
    Route::get('students', \App\Livewire\Pages\Academic\StudentIndex::class)->name('students.index');
    Route::get('classes', \App\Livewire\Pages\Academic\ClassIndex::class)->name('classes.index');
    Route::get('years', \App\Livewire\Pages\Academic\AcademicYearIndex::class)->name('years.index');

    Route::get('students/create', StudentCreate::class)->name('students.create');
    Route::get('students/{student}', StudentShow::class)->name('students.show');
    Route::get('students/{student}/edit', StudentEdit::class)->name('students.edit');

    Route::get('classes/create', ClassCreate::class)->name('classes.create');
    Route::get('classes/{schoolClass}', ClassShow::class)->name('classes.show');
    Route::get('classes/{schoolClass}/edit', ClassEdit::class)->name('classes.edit');

    Route::get('years/create', AcademicYearCreate::class)->name('years.create');
    Route::get('years/{academicYear}/edit', AcademicYearEdit::class)->name('years.edit');
});

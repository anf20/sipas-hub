<?php

use App\Http\Controllers\AcademicImportTemplateController;
use App\Livewire\Pages\Academic\AcademicDashboard;
use App\Livewire\Pages\Academic\AcademicImport;
use App\Livewire\Pages\Academic\AcademicInitializationWizard;
use App\Livewire\Pages\Academic\AcademicYearCreate;
use App\Livewire\Pages\Academic\AcademicYearEdit;
use App\Livewire\Pages\Academic\AcademicYearIndex;
use App\Livewire\Pages\Academic\ClassCreate;
use App\Livewire\Pages\Academic\ClassEdit;
use App\Livewire\Pages\Academic\ClassIndex;
use App\Livewire\Pages\Academic\ClassShow;
use App\Livewire\Pages\Academic\StudentCreate;
use App\Livewire\Pages\Academic\StudentEdit;
use App\Livewire\Pages\Academic\StudentIndex;
use App\Livewire\Pages\Academic\StudentShow;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('academic')->name('academic.')->group(function () {
    Route::get('/', AcademicDashboard::class)->name('dashboard');
    Route::get('initialization-wizard', AcademicInitializationWizard::class)->name('initialization-wizard');
    Route::get('students', StudentIndex::class)->name('students.index');
    Route::get('classes', ClassIndex::class)->name('classes.index');
    Route::get('years', AcademicYearIndex::class)->name('years.index');

    Route::get('import', AcademicImport::class)->name('import');
    Route::get('import/template/{type}', [AcademicImportTemplateController::class, 'download'])->name('import.template');

    Route::get('students/create', StudentCreate::class)->name('students.create');
    Route::get('students/{student}', StudentShow::class)->name('students.show');
    Route::get('students/{student}/edit', StudentEdit::class)->name('students.edit');

    Route::get('classes/create', ClassCreate::class)->name('classes.create');
    Route::get('classes/{schoolClass}', ClassShow::class)->name('classes.show');
    Route::get('classes/{schoolClass}/edit', ClassEdit::class)->name('classes.edit');

    Route::get('years/create', AcademicYearCreate::class)->name('years.create');
    Route::get('years/{academicYear}/edit', AcademicYearEdit::class)->name('years.edit');
});

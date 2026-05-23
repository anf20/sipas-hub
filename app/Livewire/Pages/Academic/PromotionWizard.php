<?php

namespace App\Livewire\Pages\Academic;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PromotionWizard extends Component
{
    public $sourceYearId;
    public $sourceClassId;
    public $targetYearId;
    public $targetClassId;
    public $selectedStudents = [];

    public function updatedSourceYearId()
    {
        $this->sourceClassId = null;
        $this->selectedStudents = [];
    }

    public function updatedSourceClassId()
    {
        $this->selectedStudents = [];
    }

    public function promote()
    {
        $this->validate([
            'targetClassId' => 'required',
            'selectedStudents' => 'required|array|min:1',
        ]);

        $targetClass = SchoolClass::findOrFail($this->targetClassId);
        $currentCount = $targetClass->students()->count();
        
        if ($currentCount + count($this->selectedStudents) > $targetClass->capacity) {
            session()->flash('error', __('Kapasitas kelas tujuan tidak mencukupi.'));
            return;
        }

        DB::transaction(function () use ($targetClass) {
            Student::whereIn('id', $this->selectedStudents)->update([
                'school_class_id' => $targetClass->id,
                'current_grade' => $targetClass->grade,
            ]);
        });

        $this->selectedStudents = [];
        session()->flash('status', __('Siswa berhasil dipindahkan/dinaikkan kelas.'));
    }

    public function render()
    {
        $years = AcademicYear::orderBy('name', 'desc')->get();
        $sourceClasses = $this->sourceYearId ? SchoolClass::where('academic_year_id', $this->sourceYearId)->get() : [];
        $targetClasses = $this->targetYearId ? SchoolClass::where('academic_year_id', $this->targetYearId)->get() : [];
        $studentsInSource = $this->sourceClassId ? Student::where('school_class_id', $this->sourceClassId)->get() : [];

        return view('livewire.pages.academic.promotion-wizard', [
            'years' => $years,
            'sourceClasses' => $sourceClasses,
            'targetClasses' => $targetClasses,
            'studentsInSource' => $studentsInSource,
        ]);
    }
}

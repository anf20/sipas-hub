<?php

namespace App\Livewire\Pages\Academic;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class AcademicDashboard extends Component
{
    public function render()
    {
        // Overview Data
        $totalStudents = Student::where('status', 'aktif')->count();
        $totalClasses = SchoolClass::count();
        $activeYear = AcademicYear::where('is_active', true)->first();

        // Gender Chart Data
        $genderData = Student::select('gender', DB::raw('count(*) as total'))
            ->where('status', 'aktif')
            ->groupBy('gender')
            ->get();
        $genderLabels = $genderData->pluck('gender')->map(fn ($g) => $g === 'L' ? __('Laki-laki') : __('Perempuan'))->toArray();
        $genderValues = $genderData->pluck('total')->toArray();

        // Grade Chart Data
        $gradeData = Student::select('current_grade', DB::raw('count(*) as total'))
            ->where('status', 'aktif')
            ->groupBy('current_grade')
            ->get();
        $gradeLabels = $gradeData->pluck('current_grade')->toArray();
        $gradeValues = $gradeData->pluck('total')->toArray();

        return view('livewire.pages.academic.academic-dashboard', [
            'totalStudents' => $totalStudents,
            'totalClasses' => $totalClasses,
            'activeYear' => $activeYear,
            'genderLabels' => $genderLabels,
            'genderValues' => $genderValues,
            'gradeLabels' => $gradeLabels,
            'gradeValues' => $gradeValues,
        ])->title(__('Dashboard Akademik'));
    }
}

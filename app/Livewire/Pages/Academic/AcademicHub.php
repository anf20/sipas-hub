<?php

namespace App\Livewire\Pages\Academic;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class AcademicHub extends Component
{
    use WithPagination;

    #[Url]
    public string $tab = 'overview';

    // Filters for Classes
    #[Url(as: 'c_search')]
    public string $classSearch = '';

    #[Url(as: 'c_year')]
    public string $classYearFilter = '';

    // Filters for Students
    #[Url(as: 's_search')]
    public string $studentSearch = '';

    #[Url(as: 's_class')]
    public string $studentClassFilter = '';

    public function updatedClassSearch()
    {
        $this->resetPage('classes-page');
    }

    public function updatedClassYearFilter()
    {
        $this->resetPage('classes-page');
    }

    public function updatedStudentSearch()
    {
        $this->resetPage('students-page');
    }

    public function updatedStudentClassFilter()
    {
        $this->resetPage('students-page');
    }

    public function setTab(string $tab)
    {
        $this->tab = $tab;
        $this->resetPage('classes-page');
        $this->resetPage('students-page');
    }

    // Years logic
    public function toggleYearStatus($id)
    {
        $year = AcademicYear::findOrFail($id);
        if (! $year->is_active) {
            AcademicYear::where('is_active', true)->update(['is_active' => false]);
            $year->update(['is_active' => true]);
        } else {
            $year->update(['is_active' => false]);
        }
        session()->flash('status', __('Status Tahun Ajaran diperbarui.'));
    }

    public function deleteYear($id)
    {
        $year = AcademicYear::findOrFail($id);
        if ($year->schoolClasses()->exists()) {
            session()->flash('error', __('Tidak dapat menghapus Tahun Ajaran yang memiliki data kelas.'));
            return;
        }
        $year->delete();
        session()->flash('status', __('Tahun Ajaran berhasil dihapus.'));
    }

    // Classes logic
    public function deleteClass($id)
    {
        $class = SchoolClass::findOrFail($id);
        if ($class->students()->exists()) {
            session()->flash('error', __('Tidak dapat menghapus kelas yang memiliki data siswa.'));
            return;
        }
        $class->delete();
        session()->flash('status', __('Kelas berhasil dihapus.'));
    }

    // Students logic
    public function deleteStudent($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();
        session()->flash('status', __('Data siswa berhasil dihapus.'));
    }

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
        $genderLabels = $genderData->pluck('gender')->map(fn($g) => $g === 'L' ? __('Laki-laki') : __('Perempuan'))->toArray();
        $genderValues = $genderData->pluck('total')->toArray();

        // Grade Chart Data
        $gradeData = Student::select('current_grade', DB::raw('count(*) as total'))
            ->where('status', 'aktif')
            ->groupBy('current_grade')
            ->get();
        $gradeLabels = $gradeData->pluck('current_grade')->toArray();
        $gradeValues = $gradeData->pluck('total')->toArray();

        // Classes Query with Filter and Pagination
        $classesQuery = SchoolClass::with(['academicYear', 'homeroomTeacher'])
            ->withCount('students')
            ->when($this->classSearch, function ($query) {
                $query->where('name', 'like', '%' . $this->classSearch . '%');
            })
            ->when($this->classYearFilter, function ($query) {
                $query->where('academic_year_id', $this->classYearFilter);
            })
            ->orderBy('name');

        // Students Query with Filter and Pagination
        $studentsQuery = Student::with('schoolClass')
            ->when($this->studentSearch, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->studentSearch . '%')
                      ->orWhere('nis', 'like', '%' . $this->studentSearch . '%');
                });
            })
            ->when($this->studentClassFilter, function ($query) {
                $query->where('school_class_id', $this->studentClassFilter);
            })
            ->latest();

        return view('livewire.pages.academic.academic-hub', [
            'totalStudents' => $totalStudents,
            'totalClasses' => $totalClasses,
            'activeYear' => $activeYear,
            'genderLabels' => $genderLabels,
            'genderValues' => $genderValues,
            'gradeLabels' => $gradeLabels,
            'gradeValues' => $gradeValues,
            // Lists for tabs
            'years' => AcademicYear::withCount(['schoolClasses', 'students'])->orderBy('name', 'desc')->get(),
            'classes' => $classesQuery->paginate(10, ['*'], 'classes-page'),
            'students' => $studentsQuery->paginate(10, ['*'], 'students-page'),
            'allClasses' => SchoolClass::orderBy('name')->get(),
        ])->title(__('Manajemen Akademik'));
    }
}

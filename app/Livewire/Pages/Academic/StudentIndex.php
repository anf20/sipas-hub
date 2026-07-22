<?php

namespace App\Livewire\Pages\Academic;

use App\Models\SchoolClass;
use App\Models\Student;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class StudentIndex extends Component
{
    use WithPagination;

    #[Url(as: 'search')]
    public string $search = '';

    #[Url(as: 'class')]
    public string $classFilter = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedClassFilter()
    {
        $this->resetPage();
    }

    public function deleteStudent($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();
        session()->flash('status', __('Data siswa berhasil dihapus.'));
    }

    public function render()
    {
        $studentsQuery = Student::with('schoolClass')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('nis', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->classFilter, function ($query) {
                $query->where('school_class_id', $this->classFilter);
            })
            ->latest();

        return view('livewire.pages.academic.student-index', [
            'students' => $studentsQuery->paginate(10),
            'allClasses' => SchoolClass::orderBy('name')->get(),
        ])->title(__('Data Siswa'));
    }
}

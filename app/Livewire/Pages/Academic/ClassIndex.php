<?php

namespace App\Livewire\Pages\Academic;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ClassIndex extends Component
{
    use WithPagination;

    #[Url(as: 'search')]
    public string $search = '';

    #[Url(as: 'year')]
    public string $yearFilter = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedYearFilter()
    {
        $this->resetPage();
    }

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

    public function render()
    {
        $classesQuery = SchoolClass::with(['academicYear', 'homeroomTeacher'])
            ->withCount('students')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%');
            })
            ->when($this->yearFilter, function ($query) {
                $query->where('academic_year_id', $this->yearFilter);
            })
            ->orderBy('name');

        return view('livewire.pages.academic.class-index', [
            'classes' => $classesQuery->paginate(10),
            'years' => AcademicYear::orderBy('name', 'desc')->get(),
        ])->title(__('Manajemen Kelas'));
    }
}

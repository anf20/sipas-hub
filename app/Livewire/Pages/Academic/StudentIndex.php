<?php

namespace App\Livewire\Pages\Academic;

use App\Models\Student;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class StudentIndex extends Component
{
    public function delete($id)
    {
        $student = Student::findOrFail($id);

        // Soft delete handles this, but we might want to clean up the photo if it exists
        // Though soft delete means we should probably keep the photo.
        // For now, let's just do a standard soft delete.

        $student->delete();
        session()->flash('status', __('Data siswa berhasil dihapus (soft delete).'));
    }

    public function render()
    {
        return view('livewire.pages.academic.student-index', [
            'students' => Student::with('schoolClass')->latest()->get(),
        ]);
    }
}

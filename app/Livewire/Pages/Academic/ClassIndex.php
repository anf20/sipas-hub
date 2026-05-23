<?php

namespace App\Livewire\Pages\Academic;

use App\Models\SchoolClass;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ClassIndex extends Component
{
    public function delete($id)
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
        return view('livewire.pages.academic.class-index', [
            'classes' => SchoolClass::with(['academicYear', 'homeroomTeacher'])->withCount('students')->get(),
        ]);
    }
}

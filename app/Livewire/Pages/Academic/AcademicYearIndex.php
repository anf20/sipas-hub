<?php

namespace App\Livewire\Pages\Academic;

use App\Models\AcademicYear;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class AcademicYearIndex extends Component
{
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

    public function render()
    {
        return view('livewire.pages.academic.academic-year-index', [
            'years' => AcademicYear::withCount(['schoolClasses', 'students'])->orderBy('name', 'desc')->get(),
        ])->title(__('Tahun Ajaran'));
    }
}

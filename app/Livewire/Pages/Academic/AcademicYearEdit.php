<?php

namespace App\Livewire\Pages\Academic;

use App\Models\AcademicYear;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class AcademicYearEdit extends Component
{
    public AcademicYear $academicYear;

    public $name;

    public $start_date;

    public $end_date;

    public $is_active;

    protected $rules = [
        'name' => 'required|string|max:255',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after:start_date',
        'is_active' => 'boolean',
    ];

    public function mount(AcademicYear $academicYear)
    {
        $this->academicYear = $academicYear;
        $this->name = $academicYear->name;
        $this->start_date = $academicYear->start_date;
        $this->end_date = $academicYear->end_date;
        $this->is_active = (bool) $academicYear->is_active;
    }

    public function save()
    {
        $this->validate();

        if ($this->is_active && ! $this->academicYear->is_active) {
            AcademicYear::where('is_active', true)->update(['is_active' => false]);
        }

        $this->academicYear->update([
            'name' => $this->name,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'is_active' => $this->is_active,
        ]);

        session()->flash('status', __('Tahun Ajaran berhasil diperbarui.'));

        return redirect()->route('academic.years.index');
    }

    public function render()
    {
        return view('livewire.pages.academic.academic-year-edit');
    }
}

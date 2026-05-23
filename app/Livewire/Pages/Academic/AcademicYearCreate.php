<?php

namespace App\Livewire\Pages\Academic;

use App\Models\AcademicYear;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class AcademicYearCreate extends Component
{
    public $name;

    public $start_date;

    public $end_date;

    public $is_active = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after:start_date',
        'is_active' => 'boolean',
    ];

    public function save()
    {
        $this->validate();

        if ($this->is_active) {
            AcademicYear::where('is_active', true)->update(['is_active' => false]);
        }

        AcademicYear::create([
            'name' => $this->name,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'is_active' => $this->is_active,
        ]);

        session()->flash('status', __('Tahun Ajaran berhasil ditambahkan.'));

        return redirect()->route('academic.years.index');
    }

    public function render()
    {
        return view('livewire.pages.academic.academic-year-create');
    }
}

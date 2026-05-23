<?php

namespace App\Livewire\Pages\Academic;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ClassEdit extends Component
{
    public SchoolClass $schoolClass;

    public $name;

    public $grade;

    public $major;

    public $homeroom_id;

    public $academic_year_id;

    public $capacity;

    protected $rules = [
        'name' => 'required|string|max:255',
        'grade' => 'required|string',
        'major' => 'nullable|string|max:255',
        'homeroom_id' => 'nullable|exists:users,id',
        'academic_year_id' => 'required|exists:academic_years,id',
        'capacity' => 'required|integer|min:1',
    ];

    public function mount(SchoolClass $schoolClass)
    {
        $this->schoolClass = $schoolClass;
        $this->name = $schoolClass->name;
        $this->grade = $schoolClass->grade;
        $this->major = $schoolClass->major;
        $this->homeroom_id = $schoolClass->homeroom_id;
        $this->academic_year_id = $schoolClass->academic_year_id;
        $this->capacity = $schoolClass->capacity;
    }

    public function save()
    {
        $this->validate();

        $this->schoolClass->update([
            'name' => $this->name,
            'grade' => $this->grade,
            'major' => $this->major,
            'homeroom_id' => $this->homeroom_id,
            'academic_year_id' => $this->academic_year_id,
            'capacity' => $this->capacity,
        ]);

        session()->flash('status', __('Kelas berhasil diperbarui.'));

        return redirect()->route('academic.classes.index');
    }

    public function render()
    {
        return view('livewire.pages.academic.class-edit', [
            'academicYears' => AcademicYear::orderBy('name', 'desc')->get(),
            'teachers' => User::role(['Super Admin', 'Admin Akademik'])->get(),
        ]);
    }
}

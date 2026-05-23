<?php

namespace App\Livewire\Pages\Academic;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ClassCreate extends Component
{
    public $name;

    public $grade;

    public $major;

    public $homeroom_id;

    public $academic_year_id;

    public $capacity = 30;

    protected $rules = [
        'name' => 'required|string|max:255',
        'grade' => 'required|string',
        'major' => 'nullable|string|max:255',
        'homeroom_id' => 'nullable|exists:users,id',
        'academic_year_id' => 'required|exists:academic_years,id',
        'capacity' => 'required|integer|min:1',
    ];

    public function mount()
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        if ($activeYear) {
            $this->academic_year_id = $activeYear->id;
        }
    }

    public function save()
    {
        $this->validate();

        SchoolClass::create([
            'name' => $this->name,
            'grade' => $this->grade,
            'major' => $this->major,
            'homeroom_id' => $this->homeroom_id,
            'academic_year_id' => $this->academic_year_id,
            'capacity' => $this->capacity,
        ]);

        session()->flash('status', __('Kelas berhasil ditambahkan.'));

        return redirect()->route('academic.classes.index');
    }

    public function render()
    {
        return view('livewire.pages.academic.class-create', [
            'academicYears' => AcademicYear::orderBy('name', 'desc')->get(),
            'teachers' => User::role(['Super Admin', 'Admin Akademik'])->get(), // Assuming these roles can be homeroom teachers
        ]);
    }
}

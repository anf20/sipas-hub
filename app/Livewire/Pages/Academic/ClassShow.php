<?php

namespace App\Livewire\Pages\Academic;

use App\Models\SchoolClass;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ClassShow extends Component
{
    public SchoolClass $schoolClass;

    public function mount(SchoolClass $schoolClass)
    {
        $this->schoolClass = $schoolClass->load(['academicYear', 'homeroomTeacher', 'students']);
    }

    public function render()
    {
        return view('livewire.pages.academic.class-show', [
            'students' => $this->schoolClass->students()->orderBy('name')->get(),
        ])->title(__('Detail Kelas: :name', ['name' => $this->schoolClass->name]));
    }
}

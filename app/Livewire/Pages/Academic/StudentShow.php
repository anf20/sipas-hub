<?php

namespace App\Livewire\Pages\Academic;

use App\Models\Student;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class StudentShow extends Component
{
    public Student $student;

    public function mount(Student $student)
    {
        $this->student = $student->load(['schoolClass.academicYear', 'parent', 'invoices.feeType', 'invoices.payments']);
    }

    public function render()
    {
        return view('livewire.pages.academic.student-show', [
            'student' => $this->student,
            'parent' => $this->student->parent,
            'invoices' => $this->student->invoices()->latest()->get(),
        ])->title(__('Detail Siswa: :name', ['name' => $this->student->name]));
    }
}

<?php

namespace App\Livewire\Pages\Academic;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class StudentEdit extends Component
{
    use WithFileUploads;

    public Student $student;

    public $nis;

    public $name;

    public $school_class_id;

    public $gender;

    public $birth_date;

    public $address;

    public $photo;

    public $entry_year;

    public $status;

    public $parent_user_id;

    public function mount(Student $student)
    {
        $this->student = $student;
        $this->nis = $student->nis;
        $this->name = $student->name;
        $this->school_class_id = $student->school_class_id;
        $this->gender = $student->gender;
        $this->birth_date = $student->birth_date ? $student->birth_date : null;
        $this->address = $student->address;
        $this->entry_year = $student->entry_year;
        $this->status = $student->status;
        $this->parent_user_id = $student->parent_user_id;
    }

    public function rules()
    {
        return [
            'nis' => 'required|unique:students,nis,'.$this->student->id,
            'name' => 'required|string|max:255',
            'school_class_id' => 'required|exists:school_classes,id',
            'gender' => 'required|in:L,P',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|max:1024',
            'entry_year' => 'required|numeric|digits:4',
            'status' => 'required|in:aktif,lulus,keluar,pindah',
            'parent_user_id' => 'nullable|exists:users,id',
        ];
    }

    public function save()
    {
        $this->parent_user_id = $this->parent_user_id === '' ? null : $this->parent_user_id;
        $this->birth_date = $this->birth_date === '' ? null : $this->birth_date;
        $this->address = $this->address === '' ? null : $this->address;

        $this->validate();

        $data = [
            'nis' => $this->nis,
            'name' => $this->name,
            'school_class_id' => $this->school_class_id,
            'gender' => $this->gender,
            'birth_date' => $this->birth_date,
            'address' => $this->address,
            'entry_year' => $this->entry_year,
            'status' => $this->status,
            'parent_user_id' => $this->parent_user_id,
        ];

        if ($this->photo) {
            if ($this->student->photo) {
                Storage::disk('public')->delete($this->student->photo);
            }
            $data['photo'] = $this->photo->store('students', 'public');
        }

        $this->student->update($data);

        session()->flash('status', __('Data siswa berhasil diperbarui.'));

        return redirect()->route('academic.students.index');
    }

    public function render()
    {
        return view('livewire.pages.academic.student-edit', [
            'classes' => SchoolClass::with('academicYear')->get(),
            'parents' => User::role('Orang Tua')->get(),
        ]);
    }
}

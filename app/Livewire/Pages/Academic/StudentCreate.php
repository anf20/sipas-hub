<?php

namespace App\Livewire\Pages\Academic;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class StudentCreate extends Component
{
    use WithFileUploads;

    public $nis;

    public $name;

    public $school_class_id;

    public $gender = 'L';

    public $birth_date;

    public $address;

    public $photo;

    public $entry_year;

    public $parent_user_id;

    // Form data untuk Wali Murid Baru
    public $newParentName;

    public $newParentEmail;

    public $newParentPhone;

    public function mount()
    {
        $this->entry_year = date('Y');
    }

    public function saveNewParent()
    {
        $this->validate([
            'newParentName' => 'required|string|max:255',
            'newParentEmail' => 'required|email|unique:users,email',
            'newParentPhone' => 'nullable|string|max:20',
        ]);

        $parent = User::create([
            'name' => $this->newParentName,
            'email' => $this->newParentEmail,
            'phone' => $this->newParentPhone,
            'password' => Hash::make('password123'), // Password default
        ]);

        $parent->assignRole('Orang Tua');

        // Otomatis pilih wali murid yang baru dibuat
        $this->parent_user_id = $parent->id;

        // Reset form
        $this->newParentName = '';
        $this->newParentEmail = '';
        $this->newParentPhone = '';

        $this->dispatch('close-modal', 'create-parent-modal');
        \Flux::toast(__('Akun Wali Murid berhasil dibuat dan dipilih.'), variant: 'success');
    }

    public function rules()
    {
        return [
            'nis' => 'nullable|string|unique:students,nis',
            'name' => 'required|string|max:255',
            'school_class_id' => 'required|exists:school_classes,id',
            'gender' => 'required|in:L,P',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|max:1024',
            'entry_year' => 'required|numeric|digits:4',
            'parent_user_id' => 'nullable|exists:users,id',
        ];
    }

    public function save()
    {
        $this->validate();

        $data = [
            'nis' => $this->nis,
            'name' => $this->name,
            'school_class_id' => $this->school_class_id,
            'gender' => $this->gender,
            'birth_date' => $this->birth_date,
            'address' => $this->address,
            'entry_year' => $this->entry_year,
            'status' => 'aktif',
            'parent_user_id' => $this->parent_user_id,
        ];

        if ($this->photo) {
            $data['photo'] = $this->photo->store('students', 'public');
        }

        Student::create($data);

        session()->flash('status', __('Siswa berhasil ditambahkan.'));

        return redirect()->route('academic.students.index');
    }

    public function render()
    {
        return view('livewire.pages.academic.student-create', [
            'parents' => User::role('Orang Tua')->get(),
            'classes' => SchoolClass::with('academicYear')->whereHas('academicYear', function ($query) {
                $query->where('is_active', true);
            })->get(),
        ]);
    }
}

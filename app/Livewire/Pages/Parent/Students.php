<?php

namespace App\Livewire\Pages\Parent;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.parent')]
class Students extends Component
{
    public function render()
    {
        $user = Auth::user();
        $students = $user->students()->with(['schoolClass.academicYear'])->get();

        return view('livewire.pages.parent.students', [
            'students' => $students,
        ])->title(__('Data Anak'));
    }
}

<?php

namespace App\Livewire\Pages\Management;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class UserShow extends Component
{
    public User $user;

    public function mount(User $user)
    {
        $this->user = $user->load(['roles', 'students.schoolClass.academicYear']);
    }

    public function render()
    {
        return view('livewire.pages.management.user-show')->title(__('Detail Pengguna'));
    }
}

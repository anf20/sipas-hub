<?php

namespace App\Livewire\Pages\Management;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Role;

#[Layout('layouts.app')]
class UserCreate extends Component
{
    public $name;

    public $email;

    public $password;

    public $password_confirmation;

    public $selected_roles = [];

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'selected_roles' => 'required|array|min:1',
        ];
    }

    public function save()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        $user->syncRoles($this->selected_roles);

        session()->flash('status', __('Pengguna berhasil ditambahkan.'));

        return redirect()->route('management.users.index');
    }

    public function render()
    {
        return view('livewire.pages.management.user-create', [
            'roles' => Role::all(),
        ]);
    }
}

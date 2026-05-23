<?php

namespace App\Livewire\Pages\Management;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Role;

#[Layout('layouts.app')]
class UserEdit extends Component
{
    public User $user;

    public $name;

    public $email;

    public $password;

    public $password_confirmation;

    public $selected_roles = [];

    public function mount(User $user)
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->selected_roles = $user->roles->pluck('name')->toArray();
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$this->user->id,
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'selected_roles' => 'required|array|min:1',
        ];
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        $this->user->update($data);
        $this->user->syncRoles($this->selected_roles);

        session()->flash('status', __('Pengguna berhasil diperbarui.'));

        return redirect()->route('management.users.index');
    }

    public function render()
    {
        return view('livewire.pages.management.user-edit', [
            'roles' => Role::all(),
        ]);
    }
}

<?php

namespace App\Livewire\Pages\Parent;

use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.parent')]
class Settings extends Component
{
    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
    }

    public function updateProfile()
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        Flux::toast(variant: 'success', text: __('Profil berhasil diperbarui.'));
    }

    public function updatePassword()
    {
        $validated = $this->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        Flux::toast(variant: 'success', text: __('Kata sandi berhasil diubah.'));
    }

    public function render()
    {
        return view('livewire.pages.parent.settings')->title(__('Pengaturan Akun'));
    }
}

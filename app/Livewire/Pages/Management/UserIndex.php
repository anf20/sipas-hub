<?php

namespace App\Livewire\Pages\Management;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class UserIndex extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        if (auth()->id() == $id) {
            session()->flash('error', __('Anda tidak dapat menghapus akun Anda sendiri.'));

            return;
        }

        $user = User::findOrFail($id);
        $user->delete();

        session()->flash('status', __('Pengguna berhasil dihapus.'));
    }

    public function render()
    {
        $users = User::with('roles')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.pages.management.user-index', [
            'users' => $users,
        ]);
    }
}

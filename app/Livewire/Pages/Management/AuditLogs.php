<?php

namespace App\Livewire\Pages\Management;

use App\Models\AuditLog;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class AuditLogs extends Component
{
    use WithPagination;

    public $search = '';

    public $actionFilter = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingActionFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->actionFilter = '';
        $this->resetPage();
    }

    public function render()
    {
        $logs = AuditLog::with('user')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('action', 'like', '%'.$this->search.'%')
                        ->orWhere('model_type', 'like', '%'.$this->search.'%')
                        ->orWhere('ip', 'like', '%'.$this->search.'%')
                        ->orWhereHas('user', function ($uq) {
                            $uq->where('name', 'like', '%'.$this->search.'%')
                                ->orWhere('email', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->when($this->actionFilter, function ($query) {
                $query->where('action', $this->actionFilter);
            })
            ->latest()
            ->paginate(15);

        return view('livewire.pages.management.audit-logs', [
            'logs' => $logs,
        ]);
    }
}

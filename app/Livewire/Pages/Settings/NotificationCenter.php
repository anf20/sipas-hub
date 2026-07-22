<?php

namespace App\Livewire\Pages\Settings;

use App\Models\FeeType;
use App\Models\WhatsappLog;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class NotificationCenter extends Component
{
    use WithPagination;

    #[Url(as: 'search')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $statusFilter = '';

    #[Url(as: 'fee')]
    public string $feeTypeFilter = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedFeeTypeFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $logsQuery = WhatsappLog::with(['user', 'feeType'])->orderBy('created_at', 'desc');
        
        if ($this->search) {
            $logsQuery->where(function($q) {
                $q->where('phone', 'like', '%' . $this->search . '%')
                  ->orWhereHas('user', function($uq) {
                      $uq->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }
        
        if ($this->statusFilter) {
            $logsQuery->where('status', $this->statusFilter);
        }

        if ($this->feeTypeFilter) {
            if ($this->feeTypeFilter === 'null') {
                $logsQuery->whereNull('fee_type_id');
            } else {
                $logsQuery->where('fee_type_id', $this->feeTypeFilter);
            }
        }

        // Summary stats
        $totalLogs = WhatsappLog::count();
        $sentLogs = WhatsappLog::where('status', 'sent')->count();
        $failedLogs = WhatsappLog::where('status', 'failed')->count();

        return view('livewire.pages.settings.notification-center', [
            'logs' => $logsQuery->paginate(15),
            'feeTypes' => FeeType::orderBy('name')->get(),
            'totalLogs' => $totalLogs,
            'sentLogs' => $sentLogs,
            'failedLogs' => $failedLogs,
        ])->title(__('Pusat Notifikasi'));
    }
}

<?php

namespace App\Livewire\Pages\Finance;

use App\Jobs\SendGeneralWhatsappNotification;
use App\Models\SchoolClass;
use App\Models\User;
use App\Models\WhatsappLog;
use Illuminate\Support\Facades\Bus;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class WhatsappBroadcastGeneral extends Component
{
    use WithPagination;

    public $target = 'all'; // 'all' or 'class'

    public $classId = null;

    public $messageText = '';

    public $batchId = null;

    public $search = '';

    public $statusFilter = '';

    // Status metrics
    public $totalJobs = 0;

    public $pendingJobs = 0;

    public $failedJobs = 0;

    public $processedJobs = 0;

    public $progress = 0;

    public $isDispatching = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function mount()
    {
        if (! auth()->user()->hasAnyRole(['Super Admin', 'Admin Keuangan'])) {
            abort(403);
        }

        $this->messageText = "Assalamualaikum Wr. Wb. Bapak/Ibu Wali Santri,\n\n[Tulis Pengumuman Anda Di Sini]\n\nTerima kasih.\nPengurus Pondok Pesantren";
    }

    public function startBroadcast()
    {
        $this->validate([
            'messageText' => 'required|string|min:10',
            'target' => 'required|in:all,class',
            'classId' => 'required_if:target,class|nullable|exists:school_classes,id',
        ]);

        $this->isDispatching = true;

        // Query users (parents of students)
        $query = User::whereHas('students')
            ->whereNotNull('phone')
            ->where('phone', '!=', '');

        if ($this->target === 'class') {
            $query->whereHas('students', function ($q) {
                $q->where('school_class_id', $this->classId);
            });
        }

        $users = $query->get();

        if ($users->isEmpty()) {
            \Flux::toast('Tidak ada nomor WhatsApp wali santri yang sesuai dengan kriteria.', variant: 'warning');
            $this->isDispatching = false;

            return;
        }

        $jobs = [];
        foreach ($users as $user) {
            $jobs[] = new SendGeneralWhatsappNotification($user->id, $this->messageText);
        }

        $batch = Bus::batch($jobs)
            ->name('Whatsapp General Broadcast')
            ->dispatch();

        $this->batchId = $batch->id;
        $this->isDispatching = false;

        \Flux::toast('Proses pengiriman pesan WA massal sedang berjalan!', variant: 'success');
        $this->updateBatchStatus();
    }

    public function updateBatchStatus()
    {
        if (! $this->batchId) {
            return;
        }

        $batch = Bus::findBatch($this->batchId);

        if ($batch) {
            $this->totalJobs = $batch->totalJobs;
            $this->pendingJobs = $batch->pendingJobs;
            $this->failedJobs = $batch->failedJobs;
            $this->processedJobs = $batch->processedJobs;
            $this->progress = $batch->progress();
        }
    }

    public function cancelBatch()
    {
        if ($this->batchId) {
            $batch = Bus::findBatch($this->batchId);
            if ($batch) {
                $batch->cancel();
                \Flux::toast('Proses pengiriman dihentikan.', variant: 'warning');
            }
        }
        $this->updateBatchStatus();
    }

    public function render()
    {
        $classes = SchoolClass::orderBy('name')->get();

        if ($this->batchId) {
            $this->updateBatchStatus();
        }

        // Query logs specific to general broadcast (fee_type_id is null)
        $logsQuery = WhatsappLog::with('user')
            ->whereNull('fee_type_id')
            ->orderBy('created_at', 'desc');

        if ($this->search) {
            $logsQuery->where(function ($q) {
                $q->where('phone', 'like', '%'.$this->search.'%')
                    ->orWhereHas('user', function ($uq) {
                        $uq->where('name', 'like', '%'.$this->search.'%');
                    });
            });
        }

        if ($this->statusFilter) {
            $logsQuery->where('status', $this->statusFilter);
        }

        $logs = $logsQuery->paginate(10);

        return view('livewire.pages.finance.whatsapp-broadcast-general', [
            'classes' => $classes,
            'recentLogs' => $logs,
        ])->title('Broadcast Pengumuman WhatsApp');
    }
}

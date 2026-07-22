<?php

namespace App\Livewire\Pages\Finance;

use App\Jobs\SendPaymentWhatsappNotification;
use App\Models\FeeType;
use App\Models\Invoice;
use App\Models\WhatsappLog;
use Illuminate\Support\Facades\Bus;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class WhatsappBlastFee extends Component
{
    use WithPagination;
    public FeeType $feeType;
    
    public $batchId = null;
    
    public $customMessage;
    public $monthNumber = null;
    public $monthName = '';
    
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

    public function mount(FeeType $feeType)
    {
        $this->feeType = $feeType;
        
        $month = request('month');
        if ($month && $this->feeType->category === 'SPP') {
            $this->monthNumber = $month;
            $months = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
            ];
            $this->monthName = $months[$month] ?? '';
        }
        
        $monthSuffix = $this->monthName ? ' Bulan {month_name}' : '';
        
        $this->customMessage = "Halo Bapak/Ibu Wali,\n\nTerdapat tagihan *{fee_name}$monthSuffix* yang belum dibayarkan untuk putra/putri Anda:\n{student_details}\n*Total Tunggakan: {total_amount}*\n\nMohon segera diselesaikan melalui Portal Orang Tua. Abaikan pesan ini jika sudah membayar.\nTerima kasih.";
    }

    public function startBlast()
    {
        $this->validate([
            'customMessage' => 'required|string|min:10',
        ]);
        
        $this->isDispatching = true;
        
        $query = Invoice::with(['student.parent'])
            ->where('fee_type_id', $this->feeType->id)
            ->where('status', 'unpaid')
            ->whereHas('student.parent');
            
        if (request()->has('month')) {
            $query->where('period_month', request('month'));
        }
            
        $invoices = $query->get();
            
        $grouped = $invoices->groupBy(function($invoice) {
            return $invoice->student->parent_user_id;
        });

        if ($grouped->isEmpty()) {
            \Flux::toast('Tidak ada tagihan tertunggak untuk jenis tagihan ini.', variant: 'warning');
            $this->isDispatching = false;
            return;
        }

        $jobs = [];
        foreach ($grouped as $userId => $userInvoices) {
            $invoiceIds = $userInvoices->pluck('id')->toArray();
            $jobs[] = new SendPaymentWhatsappNotification($userId, $invoiceIds, $this->customMessage);
        }

        $batch = Bus::batch($jobs)
            ->name('Whatsapp Blast: ' . $this->feeType->name)
            ->dispatch();

        $this->batchId = $batch->id;
        $this->isDispatching = false;
        
        \Flux::toast('Proses pengiriman pesan WA massal sedang berjalan!', variant: 'success');
        $this->updateBatchStatus();
    }

    public function updateBatchStatus()
    {
        if (!$this->batchId) return;

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
        // Data ringkasan spesifik untuk fee type ini
        $query = Invoice::where('fee_type_id', $this->feeType->id)
            ->where('status', 'unpaid')
            ->whereHas('student.parent');
            
        if (request()->has('month')) {
            $query->where('period_month', request('month'));
        }
            
        $summary = $query->selectRaw('count(id) as total_invoices, sum(amount) as total_amount')->first();
            
        if ($this->batchId) {
            $this->updateBatchStatus();
        }

        $logsQuery = WhatsappLog::with('user')
            ->where('fee_type_id', $this->feeType->id)
            ->orderBy('created_at', 'desc');
        
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

        $logs = $logsQuery->paginate(10);

        return view('livewire.pages.finance.whatsapp-blast-fee', [
            'summary' => $summary,
            'recentLogs' => $logs
        ])->title('Kirim Tagihan: ' . $this->feeType->name);
    }
}

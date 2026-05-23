<?php

namespace App\Livewire\Pages\Finance;

use App\Models\FeeType;
use App\Models\Invoice;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class FeeTypeShow extends Component
{
    use WithPagination;

    public FeeType $feeType;

    public $search = '';

    public $status = 'all';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function render()
    {
        $invoiceQuery = Invoice::where('fee_type_id', $this->feeType->id);

        $stats = [
            'total_count' => (clone $invoiceQuery)->count(),
            'paid_count' => (clone $invoiceQuery)->where('status', 'paid')->count(),
            'unpaid_count' => (clone $invoiceQuery)->where('status', 'unpaid')->count(),
            'total_amount' => (clone $invoiceQuery)->sum('amount'),
            'paid_amount' => (clone $invoiceQuery)->where('status', 'paid')->sum('amount'),
        ];

        $query = Invoice::where('fee_type_id', $this->feeType->id)
            ->with(['student.schoolClass'])
            ->latest();

        if ($this->status !== 'all') {
            $query->where('status', $this->status);
        }

        if (strlen($this->search) >= 2) {
            $query->whereHas('student', function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('nis', 'like', '%'.$this->search.'%');
            });
        }

        return view('livewire.pages.finance.fee-type-show', [
            'invoices' => $query->paginate(10),
            'stats' => $stats,
        ])->title(__('Detail Jenis Tagihan'));
    }
}

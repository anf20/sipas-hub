<?php

namespace App\Livewire\Pages\Finance;

use App\Models\FeeType;
use App\Models\Invoice;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class SppMonthShow extends Component
{
    use WithPagination;

    public $month;

    public $search = '';

    public $status = 'all';

    public function mount($month)
    {
        $this->month = (int) $month;
    }

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
        $academicMonths = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $monthName = $academicMonths[$this->month] ?? 'Bulan '.$this->month;

        $latestSppFeeType = FeeType::where('category', 'SPP')->latest()->first();

        // Safe fallback in case there is no SPP generated yet
        if ($latestSppFeeType) {
            $invoiceQuery = Invoice::where('fee_type_id', $latestSppFeeType->id)
                ->where('period_month', $this->month);
        } else {
            $invoiceQuery = Invoice::where('id', -1);
        }

        $stats = [
            'total_count' => (clone $invoiceQuery)->count(),
            'paid_count' => (clone $invoiceQuery)->where('status', 'paid')->count(),
            'unpaid_count' => (clone $invoiceQuery)->whereIn('status', ['unpaid', 'inactive'])->count(),
            'total_amount' => (clone $invoiceQuery)->sum('amount'),
            'paid_amount' => (clone $invoiceQuery)->where('status', 'paid')->sum('amount'),
        ];

        $query = (clone $invoiceQuery)
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

        return view('livewire.pages.finance.spp-month-show', [
            'invoices' => $query->paginate(10),
            'stats' => $stats,
            'monthName' => $monthName,
        ])->title(__('Detail SPP '.$monthName));
    }
}

<?php

namespace App\Livewire\Pages\Finance;

use App\Jobs\GenerateInvoices;
use App\Models\AuditLog;
use App\Models\FeeType;
use App\Models\Invoice;
use App\Models\Payment;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class FinanceHub extends Component
{
    use WithPagination;

    #[Url]
    public string $tab = 'overview';

    // SPP Generation Fields
    public $month;

    public $year;

    public $default_amount = 0;

    public $due_date;

    public function mount()
    {
        $this->month = (int) date('n');
        $this->year = (int) date('Y');
        $this->due_date = date('Y-m-d', strtotime('+10 days'));
    }

    public function setTab(string $tab)
    {
        $this->tab = $tab;
        $this->resetPage();
    }

    // SPP Logic
    public function generateSpp()
    {
        $this->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer',
            'default_amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
        ]);

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $monthName = $months[(int) $this->month] ?? 'Unknown';
        $name = "SPP Bulan {$monthName} {$this->year}";

        $feeType = FeeType::create([
            'name' => $name,
            'category' => 'SPP',
            'default_amount' => $this->default_amount,
            'is_recurring' => true,
            'recurrence' => 'bulanan',
            'applicable_grades' => null,
            'is_active' => true,
        ]);

        GenerateInvoices::dispatchSync(
            $feeType->id,
            (int) $this->month,
            (int) $this->year,
            $this->due_date,
            ['type' => 'all', 'value' => null],
            auth()->id()
        );

        \Flux::toast(__('Tagihan SPP berhasil digenerate.'), variant: 'success');
        $this->dispatch('close-modal', 'generate-spp-modal');
    }

    // Fee Type Logic
    public function toggleFeeStatus($id)
    {
        $feeType = FeeType::findOrFail($id);
        $feeType->update(['is_active' => ! $feeType->is_active]);
        \Flux::toast(__('Status tagihan diperbarui.'), variant: 'success');
    }

    public function deleteFee($id)
    {
        $feeType = FeeType::findOrFail($id);
        $feeType->delete();
        \Flux::toast(__('Data berhasil dihapus.'), variant: 'success');
    }

    public function render()
    {
        // Overview Data
        $revenueThisMonth = Payment::whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        $revenueLastMonth = Payment::whereMonth('paid_at', now()->subMonth()->month)
            ->whereYear('paid_at', now()->subMonth()->year)
            ->sum('amount');

        $totalUnpaid = Invoice::where('status', 'unpaid')->sum('amount');

        // Payment Methods Data
        $manualMethods = ['cash', 'transfer'];
        $manualPayments = Payment::whereIn('method', $manualMethods)->sum('amount');
        $onlinePayments = Payment::whereNotIn('method', $manualMethods)->sum('amount');

        // Collection Rate
        $totalInvoiced = Invoice::sum('amount');
        $totalPaid = Payment::sum('amount');
        $collectionRate = $totalInvoiced > 0 ? round(($totalPaid / $totalInvoiced) * 100, 1) : 0;

        // SPP Specific Summary Metrics
        $sppQuery = Invoice::whereHas('feeType', function ($query) {
            $query->where('category', 'SPP');
        });

        $sppTotalInvoiced = (float) $sppQuery->sum('amount');
        $sppTotalInvoicedCount = $sppQuery->count();

        $sppTotalPaid = (float) Payment::whereHas('invoice.feeType', function ($query) {
            $query->where('category', 'SPP');
        })->sum('amount');
        $sppTotalPaidCount = Invoice::where('status', 'paid')->whereHas('feeType', function ($query) {
            $query->where('category', 'SPP');
        })->count();

        $sppTotalUnpaid = (float) Invoice::where('status', 'unpaid')->whereHas('feeType', function ($query) {
            $query->where('category', 'SPP');
        })->sum('amount');
        $sppTotalUnpaidCount = Invoice::where('status', 'unpaid')->whereHas('feeType', function ($query) {
            $query->where('category', 'SPP');
        })->count();

        $sppCollectionRate = $sppTotalInvoiced > 0 ? round(($sppTotalPaid / $sppTotalInvoiced) * 100, 1) : 0;

        // Other Fees Specific Summary Metrics
        $otherQuery = Invoice::whereHas('feeType', function ($query) {
            $query->where('category', '!=', 'SPP');
        });

        $otherTotalInvoiced = (float) $otherQuery->sum('amount');
        $otherTotalInvoicedCount = $otherQuery->count();

        $otherTotalPaid = (float) Payment::whereHas('invoice.feeType', function ($query) {
            $query->where('category', '!=', 'SPP');
        })->sum('amount');
        $otherTotalPaidCount = Invoice::where('status', 'paid')->whereHas('feeType', function ($query) {
            $query->where('category', '!=', 'SPP');
        })->count();

        $otherTotalUnpaid = (float) Invoice::where('status', 'unpaid')->whereHas('feeType', function ($query) {
            $query->where('category', '!=', 'SPP');
        })->sum('amount');
        $otherTotalUnpaidCount = Invoice::where('status', 'unpaid')->whereHas('feeType', function ($query) {
            $query->where('category', '!=', 'SPP');
        })->count();

        $otherCollectionRate = $otherTotalInvoiced > 0 ? round(($otherTotalPaid / $otherTotalInvoiced) * 100, 1) : 0;

        return view('livewire.pages.finance.finance-hub', [
            'revenueThisMonth' => $revenueThisMonth,
            'revenueLastMonth' => $revenueLastMonth,
            'totalUnpaid' => $totalUnpaid,
            'manualPayments' => $manualPayments,
            'onlinePayments' => $onlinePayments,
            'collectionRate' => $collectionRate,
            'sppTotalInvoiced' => $sppTotalInvoiced,
            'sppTotalInvoicedCount' => $sppTotalInvoicedCount,
            'sppTotalPaid' => $sppTotalPaid,
            'sppTotalPaidCount' => $sppTotalPaidCount,
            'sppTotalUnpaid' => $sppTotalUnpaid,
            'sppTotalUnpaidCount' => $sppTotalUnpaidCount,
            'sppCollectionRate' => $sppCollectionRate,
            'otherTotalInvoiced' => $otherTotalInvoiced,
            'otherTotalInvoicedCount' => $otherTotalInvoicedCount,
            'otherTotalPaid' => $otherTotalPaid,
            'otherTotalPaidCount' => $otherTotalPaidCount,
            'otherTotalUnpaid' => $otherTotalUnpaid,
            'otherTotalUnpaidCount' => $otherTotalUnpaidCount,
            'otherCollectionRate' => $otherCollectionRate,
            'sppBatches' => FeeType::where('category', 'SPP')->latest()->get(),
            'otherFees' => FeeType::where('category', '!=', 'SPP')
                ->withCount(['invoices as total_target', 'invoices as paid_target' => function ($q) {
                    $q->where('status', 'paid');
                }])
                ->latest()->get(),
            'months' => [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
            ],
            // For Reports
            'recentTransactions' => Payment::with(['invoice.student', 'invoice.feeType'])->latest()->limit(10)->get(),
            'auditLogs' => AuditLog::with('user')->latest()->limit(20)->get(),
        ])->title(__('Manajemen Keuangan'));
    }
}

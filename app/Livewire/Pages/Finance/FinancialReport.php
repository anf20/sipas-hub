<?php

namespace App\Livewire\Pages\Finance;

use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class FinancialReport extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public $startDate;

    #[Url(history: true)]
    public $endDate;

    #[Url(history: true)]
    public $category = 'all'; // all, SPP, Non-SPP

    #[Url(history: true)]
    public $paymentMethod = 'all'; // all, midtrans, manual

    #[Url(history: true)]
    public $search = '';

    public function mount()
    {
        $this->startDate = $this->startDate ?: now()->startOfMonth()->format('Y-m-d');
        $this->endDate = $this->endDate ?: now()->endOfMonth()->format('Y-m-d');
    }

    public function updated($property)
    {
        if (in_array($property, ['startDate', 'endDate', 'category', 'paymentMethod', 'search'])) {
            $this->resetPage('ledgerPage');
        }
    }

    protected function getFilteredPaymentsQuery()
    {
        $query = Payment::whereBetween('paid_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59'])
            ->where(function($q) {
                // To support both midtrans success or manual
                $q->where('status', 'success')->orWhereNull('status'); 
            });

        if ($this->paymentMethod !== 'all') {
            if ($this->paymentMethod === 'midtrans') {
                $query->where('method', '!=', 'manual')->where('method', '!=', 'cash');
            } else {
                $query->whereIn('method', ['manual', 'cash']);
            }
        }

        if ($this->category !== 'all') {
            $query->whereHas('invoice.feeType', function ($q) {
                if ($this->category === 'SPP') {
                    $q->where('category', 'SPP');
                } else {
                    $q->where('category', '!=', 'SPP');
                }
            });
        }

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('receipt_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('invoice.student', function ($q2) {
                      $q2->where('name', 'like', '%' . $this->search . '%')
                         ->orWhere('nis', 'like', '%' . $this->search . '%');
                  });
            });
        }

        return $query;
    }

    protected function getFilteredInvoicesQuery()
    {
        // Gunakan due_date alih-alih created_at agar tagihan bulan depan tidak masuk hitungan "Target"
        $query = Invoice::whereBetween('due_date', [$this->startDate, $this->endDate]);

        if ($this->category !== 'all') {
            $query->whereHas('feeType', function ($q) {
                if ($this->category === 'SPP') {
                    $q->where('category', 'SPP');
                } else {
                    $q->where('category', '!=', 'SPP');
                }
            });
        }

        return $query;
    }

    #[Computed]
    public function kpi()
    {
        $totalPenerimaan = (clone $this->getFilteredPaymentsQuery())->sum('amount');
        
        // Pembayaran di muka: Pembayaran yang masuk di periode ini, tapi untuk invoice yang jatuh temponya masih di masa depan
        $advancePayment = (clone $this->getFilteredPaymentsQuery())
            ->whereHas('invoice', function ($q) {
                $q->where('due_date', '>', $this->endDate);
            })->sum('amount');

        // Tunggakan Jatuh Tempo: Semua invoice unpaid yang due_date-nya sudah lewat dari endDate (akumulasi hutang lama)
        $tunggakanQuery = Invoice::where('status', 'unpaid')
            ->whereDate('due_date', '<=', $this->endDate)
            ->where('status', '!=', 'inactive');
            
        if ($this->category !== 'all') {
            $tunggakanQuery->whereHas('feeType', function ($q) {
                if ($this->category === 'SPP') {
                    $q->where('category', 'SPP');
                } else {
                    $q->where('category', '!=', 'SPP');
                }
            });
        }
        $totalTunggakanJatuhTempo = $tunggakanQuery->sum('amount');
        
        // Tagihan Masa Depan: Invoice yang belum jatuh tempo (due_date > endDate) yang berstatus unpaid/inactive
        $tagihanMasaDepan = Invoice::where('due_date', '>', $this->endDate)
            ->whereIn('status', ['unpaid', 'inactive'])
            ->sum('amount');

        $totalInvoicesCount = (clone $this->getFilteredInvoicesQuery())->where('status', '!=', 'inactive')->count();
        $paidInvoicesCount = (clone $this->getFilteredInvoicesQuery())->where('status', 'paid')->count();
        
        $collectionRate = $totalInvoicesCount > 0 ? round(($paidInvoicesCount / $totalInvoicesCount) * 100, 1) : 0;

        return [
            'penerimaan' => $totalPenerimaan,
            'advance_payment' => $advancePayment,
            'tunggakan_jatuh_tempo' => $totalTunggakanJatuhTempo,
            'collection_rate' => $collectionRate,
            'tagihan_masa_depan' => $tagihanMasaDepan,
        ];
    }

    #[Computed]
    public function rekapKategori()
    {
        $invoices = (clone $this->getFilteredInvoicesQuery())
            ->where('status', '!=', 'inactive')
            ->with('feeType')
            ->get();

        $grouped = $invoices->groupBy(function ($invoice) {
            return $invoice->feeType ? $invoice->feeType->name : 'Lainnya';
        });

        $rekap = [];
        foreach ($grouped as $categoryName => $items) {
            $target = $items->sum('amount');
            $paid = $items->where('status', 'paid')->sum('amount');
            $unpaid = $items->where('status', 'unpaid')->sum('amount');
            
            $rate = $target > 0 ? round(($paid / $target) * 100, 1) : 0;

            $rekap[] = [
                'name' => $categoryName,
                'target' => $target,
                'paid' => $paid,
                'unpaid' => $unpaid,
                'rate' => $rate,
            ];
        }

        usort($rekap, fn($a, $b) => $b['target'] <=> $a['target']);
        return $rekap;
    }

    #[Computed]
    public function rekapSpp()
    {
        if ($this->category === 'Non-SPP') {
            return [];
        }

        $invoices = (clone $this->getFilteredInvoicesQuery())
            ->where('status', '!=', 'inactive')
            ->whereHas('feeType', fn($q) => $q->where('category', 'SPP'))
            ->get();

        $grouped = $invoices->groupBy(function ($invoice) {
            return $invoice->period_year . '-' . str_pad($invoice->period_month, 2, '0', STR_PAD_LEFT);
        });

        $rekap = [];
        foreach ($grouped as $period => $items) {
            if (!$items->first()->period_month) continue; // Skip if null
            
            $month = (int) substr($period, 5, 2);
            $year = substr($period, 0, 4);
            $monthName = Carbon::create()->month($month)->translatedFormat('F');

            $target = $items->sum('amount');
            $paid = $items->where('status', 'paid')->sum('amount');
            $unpaid = $items->where('status', 'unpaid')->sum('amount');
            $countTotal = $items->count();
            $countPaid = $items->where('status', 'paid')->count();
            
            $rate = $countTotal > 0 ? round(($countPaid / $countTotal) * 100, 1) : 0;

            $rekap[] = [
                'period' => "{$monthName} {$year}",
                'target' => $target,
                'paid' => $paid,
                'unpaid' => $unpaid,
                'ratio' => "{$countPaid} / {$countTotal} Siswa",
                'rate' => $rate,
                'sort_key' => $period,
            ];
        }

        usort($rekap, fn($a, $b) => $a['sort_key'] <=> $b['sort_key']);
        return $rekap;
    }

    #[Computed]
    public function ledger()
    {
        return $this->getFilteredPaymentsQuery()
            ->with(['invoice.student', 'invoice.feeType', 'recorder'])
            ->orderBy('paid_at', 'desc')
            ->paginate(10, ['*'], 'ledgerPage');
    }

    public function exportPdf()
    {
        return redirect()->route('finance.reports.financial.pdf', [
            'start' => $this->startDate,
            'end' => $this->endDate,
            'category' => $this->category,
            'method' => $this->paymentMethod,
            'search' => $this->search,
        ]);
    }

    public function render()
    {
        return view('livewire.pages.finance.financial-report')->title(__('Laporan Keuangan'));
    }
}

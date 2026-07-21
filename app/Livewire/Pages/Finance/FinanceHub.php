<?php

namespace App\Livewire\Pages\Finance;

use App\Jobs\GenerateYearlyInvoiceJob;
use App\Models\AuditLog;
use App\Models\FeeType;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
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

    // Global Filters for KPIs
    public $filterYear;
    public $filterMonth;

    // Generation Modal variables
    public $month = 7;
    public $year;
    public $default_amount = 0;

    public function mount()
    {
        $this->year = (int) date('Y');
        
        $this->filterYear = (int) date('Y');
        $this->filterMonth = (int) date('n');
    }

    public function setTab(string $tab)
    {
        $this->tab = $tab;
        $this->resetPage();
    }

    public function generateSpp()
    {
        $this->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer',
            'default_amount' => 'required|numeric|min:0',
        ]);

        $startYear = (int) $this->year;
        $endYear = $startYear + 1;
        $name = "SPP Tahun Ajaran {$startYear}/{$endYear}";

        $feeType = FeeType::create([
            'name' => $name,
            'category' => 'SPP',
            'default_amount' => $this->default_amount,
            'is_recurring' => true,
            'recurrence' => 'tahunan',
            'applicable_grades' => null,
            'is_active' => true,
        ]);

        $activeStudents = Student::where('status', 'aktif')->pluck('id');
        $adminId = auth()->id();

        foreach ($activeStudents as $studentId) {
            GenerateYearlyInvoiceJob::dispatch(
                $studentId,
                $feeType->id,
                $this->default_amount,
                (int) $this->month,
                $startYear,
                $adminId
            );
        }

        \Flux::toast(__('Proses background pembuatan SPP 1 Tahun untuk '.$activeStudents->count().' siswa sedang berjalan!'), variant: 'success');
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
        $activeStartYear = date('n') >= 7 ? (int) date('Y') : (int) date('Y') - 1;
        $activeEndYear = $activeStartYear + 1;

        $applyAcademicYear = function ($query) use ($activeStartYear, $activeEndYear) {
            return $query->where(function ($q) use ($activeStartYear, $activeEndYear) {
                $q->where(function ($sub) use ($activeStartYear) {
                    $sub->where('period_year', $activeStartYear)->where('period_month', '>=', 7);
                })->orWhere(function ($sub) use ($activeEndYear) {
                    $sub->where('period_year', $activeEndYear)->where('period_month', '<=', 6);
                });
            })->where('status', '!=', 'inactive');
        };

        // ==========================================
        // KARTU OVERVIEW UTAMA (Ringkasan Keuangan Tahun Ajaran Ini)
        // ==========================================
        
        $baseSppQuery = $applyAcademicYear(Invoice::whereHas('feeType', function ($q) {
            $q->where('category', 'SPP');
        }));

        // A. METRIK 1: PROYEKSI PIUTANG SPP TAHUN AJARAN INI
        $proyeksiPiutangBulanIni = (float) (clone $baseSppQuery)->sum('amount');

        // B. METRIK 2: TINGKAT PELUNASAN SPP
        $totalSppInvoicedFilter = (clone $baseSppQuery)->count();
        $totalSppPaidFilter = (clone $baseSppQuery)->where('status', 'paid')->count();
        $sppCollectionRateCard = $totalSppInvoicedFilter > 0 ? round(($totalSppPaidFilter / $totalSppInvoicedFilter) * 100, 1) : 0;

        // C. METRIK 3: TOTAL TUNGGAKAN LAINNYA (NON-SPP)
        $tunggakanNonSpp = (float) $applyAcademicYear(Invoice::whereHas('feeType', function ($query) {
            $query->where('category', '!=', 'SPP');
        }))->where('status', 'unpaid')->sum('amount');

        // D. BOX OVERVIEW TAGIHAN (SPP SAJA TAHUN INI)
        $boxTotalDitagihkanCount = (clone $baseSppQuery)->count();
        $boxTotalDitagihkan = (float) (clone $baseSppQuery)->sum('amount');

        $boxTotalLunasCount = (clone $baseSppQuery)->where('status', 'paid')->count();
        $boxTotalLunas = (float) (clone $baseSppQuery)->where('status', 'paid')->sum('amount');

        $boxTotalTunggakan = $boxTotalDitagihkan - $boxTotalLunas;
        $boxTotalTunggakanCount = $boxTotalDitagihkanCount - $boxTotalLunasCount;

        // TABEL RINGKASAN SPP PER BULAN
        $sppMonthlyTable = Invoice::whereHas('feeType', function ($q) {
            $q->where('category', 'SPP');
        })
        ->where(function ($q) use ($activeStartYear, $activeEndYear) {
            $q->where(function ($sub) use ($activeStartYear) {
                $sub->where('period_year', $activeStartYear)->where('period_month', '>=', 7);
            })->orWhere(function ($sub) use ($activeEndYear) {
                $sub->where('period_year', $activeEndYear)->where('period_month', '<=', 6);
            });
        })
        ->where('status', '!=', 'inactive')
        ->selectRaw("
            period_year,
            period_month,
            SUM(CASE WHEN status = 'paid' THEN amount ELSE 0 END) as total_paid,
            SUM(CASE WHEN status = 'unpaid' THEN amount ELSE 0 END) as total_unpaid,
            SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as count_paid,
            COUNT(id) as count_total
        ")
        ->groupBy('period_year', 'period_month')
        ->orderByDesc('period_year')
        ->orderByDesc('period_month')
        ->paginate(6, ['*'], 'sppPage');



        // ==========================================


        // Payment Methods Data (Old usage)
        $manualMethods = ['cash', 'transfer'];
        $manualPayments = Payment::whereIn('method', $manualMethods)->sum('amount');
        $onlinePayments = Payment::whereNotIn('method', $manualMethods)->sum('amount');

        // Collection Rate
        $totalInvoiced = Invoice::sum('amount');
        $totalPaid = Payment::sum('amount');
        $collectionRate = $totalInvoiced > 0 ? round(($totalPaid / $totalInvoiced) * 100, 1) : 0;

        // SPP Specific Summary Metrics (Bulan Ini & Bulan Lalu)
        $currentDate = now();
        $lastMonthDate = now()->subMonth();

        $sppCurrentMonthQuery = Invoice::whereHas('feeType', function ($query) {
            $query->where('category', 'SPP');
        })->where('period_year', $currentDate->year)
          ->where('period_month', $currentDate->month)
          ->where('status', '!=', 'inactive');

        $sppLastMonthQuery = Invoice::whereHas('feeType', function ($query) {
            $query->where('category', 'SPP');
        })->where('period_year', $lastMonthDate->year)
          ->where('period_month', $lastMonthDate->month)
          ->where('status', '!=', 'inactive');

        // Bulan Ini
        $sppTotalInvoiced = (float) (clone $sppCurrentMonthQuery)->sum('amount');
        $sppTotalInvoicedCount = (clone $sppCurrentMonthQuery)->count();
        $sppTotalPaid = (float) (clone $sppCurrentMonthQuery)->where('status', 'paid')->sum('amount');
        $sppTotalPaidCount = (clone $sppCurrentMonthQuery)->where('status', 'paid')->count();
        $sppTotalUnpaid = (float) (clone $sppCurrentMonthQuery)->where('status', 'unpaid')->sum('amount');
        $sppTotalUnpaidCount = (clone $sppCurrentMonthQuery)->where('status', 'unpaid')->count();

        // Bulan Lalu
        $sppLastMonthInvoiced = (float) (clone $sppLastMonthQuery)->sum('amount');
        $sppLastMonthInvoicedCount = (clone $sppLastMonthQuery)->count();
        $sppLastMonthPaid = (float) (clone $sppLastMonthQuery)->where('status', 'paid')->sum('amount');
        $sppLastMonthPaidCount = (clone $sppLastMonthQuery)->where('status', 'paid')->count();
        $sppLastMonthUnpaid = (float) (clone $sppLastMonthQuery)->where('status', 'unpaid')->sum('amount');
        $sppLastMonthUnpaidCount = (clone $sppLastMonthQuery)->where('status', 'unpaid')->count();

        // Other Fees Specific Summary Metrics
        $otherQuery = $applyAcademicYear(Invoice::whereHas('feeType', function ($query) {
            $query->where('category', '!=', 'SPP');
        }));

        $otherTotalInvoiced = (float) (clone $otherQuery)->sum('amount');
        $otherTotalInvoicedCount = (clone $otherQuery)->count();

        $otherTotalPaid = (float) Payment::whereHas('invoice', function ($q) use ($applyAcademicYear) {
            $applyAcademicYear($q)->whereHas('feeType', function ($q2) {
                $q2->where('category', '!=', 'SPP');
            });
        })->sum('amount');
        $otherTotalPaidCount = (clone $otherQuery)->where('status', 'paid')->count();

        $otherTotalUnpaid = (float) (clone $otherQuery)->where('status', 'unpaid')->sum('amount');
        $otherTotalUnpaidCount = (clone $otherQuery)->where('status', 'unpaid')->count();

        $otherCollectionRate = $otherTotalInvoiced > 0 ? round(($otherTotalPaid / $otherTotalInvoiced) * 100, 1) : 0;

        $academicMonths = [
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
        ];

        $sppMonthsData = [];
        $latestSppFeeType = FeeType::where('category', 'SPP')->latest()->first();

        if ($latestSppFeeType) {
            foreach ($academicMonths as $mNum => $mName) {
                $monthInvoices = Invoice::where('fee_type_id', $latestSppFeeType->id)->where('period_month', $mNum);

                $sppMonthsData[] = [
                    'month_number' => $mNum,
                    'month_name' => $mName,
                    'total_target' => (clone $monthInvoices)->count(),
                    'paid_target' => (clone $monthInvoices)->where('status', 'paid')->count(),
                ];
            }
        }

        return view('livewire.pages.finance.finance-hub', [
            'proyeksiPiutangBulanIni' => $proyeksiPiutangBulanIni,
            'sppCollectionRateCard' => $sppCollectionRateCard,
            'tunggakanNonSpp' => $tunggakanNonSpp,
            'boxTotalDitagihkan' => $boxTotalDitagihkan,
            'boxTotalDitagihkanCount' => $boxTotalDitagihkanCount,
            'boxTotalLunas' => $boxTotalLunas,
            'boxTotalLunasCount' => $boxTotalLunasCount,
            'activeStartYear' => $activeStartYear,
            'activeEndYear' => $activeEndYear,
            'boxTotalTunggakan' => $boxTotalTunggakan,
            'boxTotalTunggakanCount' => $boxTotalTunggakanCount,
            
            'activeFeeArrears' => FeeType::where('is_active', true)
                ->where('category', '!=', 'SPP')
                ->withCount([
                    'invoices as total_target',
                    'invoices as paid_target' => function ($q) {
                        $q->where('status', 'paid');
                    },
                    'invoices as unpaid_target' => function ($q) {
                        $q->where('status', 'unpaid');
                    }
                ])
                ->withSum(['invoices as unpaid_amount' => function ($q) {
                    $q->where('status', 'unpaid');
                }], 'amount')
                ->latest()
                ->get(),
            'sppMonthlyTable' => $sppMonthlyTable,
            'manualPayments' => $manualPayments,
            'onlinePayments' => $onlinePayments,
            'collectionRate' => $collectionRate,
            'sppTotalInvoiced' => $sppTotalInvoiced,
            'sppTotalInvoicedCount' => $sppTotalInvoicedCount,
            'sppTotalPaid' => $sppTotalPaid,
            'sppTotalPaidCount' => $sppTotalPaidCount,
            'sppTotalUnpaid' => $sppTotalUnpaid,
            'sppTotalUnpaidCount' => $sppTotalUnpaidCount,
            
            'sppLastMonthInvoiced' => $sppLastMonthInvoiced,
            'sppLastMonthInvoicedCount' => $sppLastMonthInvoicedCount,
            'sppLastMonthPaid' => $sppLastMonthPaid,
            'sppLastMonthPaidCount' => $sppLastMonthPaidCount,
            'sppLastMonthUnpaid' => $sppLastMonthUnpaid,
            'sppLastMonthUnpaidCount' => $sppLastMonthUnpaidCount,
            'otherTotalInvoiced' => $otherTotalInvoiced,
            'otherTotalInvoicedCount' => $otherTotalInvoicedCount,
            'otherTotalPaid' => $otherTotalPaid,
            'otherTotalPaidCount' => $otherTotalPaidCount,
            'otherTotalUnpaid' => $otherTotalUnpaid,
            'otherTotalUnpaidCount' => $otherTotalUnpaidCount,
            'otherCollectionRate' => $otherCollectionRate,
            'sppMonths' => $sppMonthsData,
            'otherFees' => FeeType::where('category', '!=', 'SPP')
                ->withCount(['invoices as total_target', 'invoices as paid_target' => function ($q) {
                    $q->where('status', 'paid');
                }])
                ->latest()->get(),
            'latestSppFeeType' => $latestSppFeeType,
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

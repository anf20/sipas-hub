<?php

namespace App\Livewire\Pages\Finance;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\SchoolClass;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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

    #[Url(history: true)]
    public $trendType = 'daily'; // daily, monthly

    #[Url(history: true)]
    public $selectedClass = 'all';

    #[Url(history: true)]
    public $printStartDate;

    #[Url(history: true)]
    public $printEndDate;

    #[Url(history: true)]
    public $printCategory = 'all';

    #[Url(history: true)]
    public $printPaymentMethod = 'all';

    #[Url(history: true)]
    public $printSelectedClass = 'all';

    #[Url(history: true)]
    public $printSearch = '';

    public $printPreviewTab = 'summary'; // summary, details

    public $showProofModal = false;

    public $proofFileUrl = null;

    public function viewProof($paymentId)
    {
        $payment = Payment::find($paymentId);
        if ($payment && $payment->proof_file) {
            $this->proofFileUrl = asset('storage/'.$payment->proof_file);
            $this->showProofModal = true;
        }
    }

    public function mount()
    {
        $this->startDate = $this->startDate ?: now()->startOfMonth()->format('Y-m-d');
        $this->endDate = $this->endDate ?: now()->endOfMonth()->format('Y-m-d');

        $this->printStartDate = $this->printStartDate ?: now()->startOfMonth()->format('Y-m-d');
        $this->printEndDate = $this->printEndDate ?: now()->endOfMonth()->format('Y-m-d');
    }

    public function updated($property)
    {
        if (in_array($property, ['startDate', 'endDate', 'category', 'paymentMethod', 'search', 'trendType', 'selectedClass'])) {
            $this->resetPage('ledgerPage');
        }
    }

    protected function getFilteredPaymentsQuery()
    {
        $query = Payment::whereBetween('paid_at', [$this->startDate.' 00:00:00', $this->endDate.' 23:59:59'])
            ->where(function ($q) {
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

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('receipt_number', 'like', '%'.$this->search.'%')
                    ->orWhereHas('invoice.student', function ($q2) {
                        $q2->where('name', 'like', '%'.$this->search.'%')
                            ->orWhere('nis', 'like', '%'.$this->search.'%');
                    });
            });
        }

        if ($this->selectedClass !== 'all') {
            if (str_starts_with($this->selectedClass, 'grade-')) {
                $grade = str_replace('grade-', '', $this->selectedClass);
                $query->whereHas('invoice.student.schoolClass', function ($q) use ($grade) {
                    $q->where('grade', $grade);
                });
            } elseif (str_starts_with($this->selectedClass, 'class-')) {
                $classId = str_replace('class-', '', $this->selectedClass);
                $query->whereHas('invoice.student', function ($q) use ($classId) {
                    $q->where('school_class_id', $classId);
                });
            }
        }

        return $query;
    }

    protected function getFilteredInvoicesQuery()
    {
        // Gunakan due_date alih-alih created_at agar tagihan bulan depan tidak masuk hitungan "Target"
        $query = Invoice::whereDate('due_date', '>=', $this->startDate)
            ->whereDate('due_date', '<=', $this->endDate);

        if ($this->category !== 'all') {
            $query->whereHas('feeType', function ($q) {
                if ($this->category === 'SPP') {
                    $q->where('category', 'SPP');
                } else {
                    $q->where('category', '!=', 'SPP');
                }
            });
        }

        if ($this->selectedClass !== 'all') {
            if (str_starts_with($this->selectedClass, 'grade-')) {
                $grade = str_replace('grade-', '', $this->selectedClass);
                $query->whereHas('student.schoolClass', function ($q) use ($grade) {
                    $q->where('grade', $grade);
                });
            } elseif (str_starts_with($this->selectedClass, 'class-')) {
                $classId = str_replace('class-', '', $this->selectedClass);
                $query->whereHas('student', function ($q) use ($classId) {
                    $q->where('school_class_id', $classId);
                });
            }
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
                $q->whereDate('due_date', '>', $this->endDate);
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
        $tagihanMasaDepan = Invoice::whereDate('due_date', '>', $this->endDate)
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

    protected function getUnfilteredPaymentsQuery()
    {
        return Payment::where(function ($q) {
            // To support both midtrans success or manual
            $q->where('status', 'success')->orWhereNull('status');
        });
    }

    #[Computed]
    public function trendData()
    {
        $driver = DB::connection()->getDriverName();

        if ($this->trendType === 'daily') {
            $selectRaw = match ($driver) {
                'sqlite' => "strftime('%Y-%m-%d', paid_at) as label_raw, SUM(amount) as total_amount, COUNT(id) as transaction_count",
                'mysql' => 'DATE(paid_at) as label_raw, SUM(amount) as total_amount, COUNT(id) as transaction_count',
                'pgsql' => "TO_CHAR(paid_at, 'YYYY-MM-DD') as label_raw, SUM(amount) as total_amount, COUNT(id) as transaction_count",
                default => 'DATE(paid_at) as label_raw, SUM(amount) as total_amount, COUNT(id) as transaction_count',
            };

            $groupByRaw = match ($driver) {
                'sqlite' => "strftime('%Y-%m-%d', paid_at)",
                'mysql' => 'DATE(paid_at)',
                'pgsql' => "TO_CHAR(paid_at, 'YYYY-MM-DD')",
                default => 'DATE(paid_at)',
            };

            $data = $this->getUnfilteredPaymentsQuery()
                ->where('paid_at', '>=', now()->subDays(30)->startOfDay())
                ->selectRaw($selectRaw)
                ->groupByRaw($groupByRaw)
                ->orderByRaw("$groupByRaw asc")
                ->get();

            return $data->map(function ($item) {
                return [
                    'label' => Carbon::parse($item->label_raw)->translatedFormat('d M Y'),
                    'label_raw' => $item->label_raw,
                    'total_amount' => (float) $item->total_amount,
                    'count' => $item->transaction_count,
                ];
            })->all();
        } else {
            $selectRaw = match ($driver) {
                'sqlite' => "strftime('%Y-%m', paid_at) as label_raw, SUM(amount) as total_amount, COUNT(id) as transaction_count",
                'mysql' => "DATE_FORMAT(paid_at, '%Y-%m') as label_raw, SUM(amount) as total_amount, COUNT(id) as transaction_count",
                'pgsql' => "TO_CHAR(paid_at, 'YYYY-MM') as label_raw, SUM(amount) as total_amount, COUNT(id) as transaction_count",
                default => "DATE_FORMAT(paid_at, '%Y-%m') as label_raw, SUM(amount) as total_amount, COUNT(id) as transaction_count",
            };

            $groupByRaw = match ($driver) {
                'sqlite' => "strftime('%Y-%m', paid_at)",
                'mysql' => "DATE_FORMAT(paid_at, '%Y-%m')",
                'pgsql' => "TO_CHAR(paid_at, 'YYYY-MM')",
                default => "DATE_FORMAT(paid_at, '%Y-%m')",
            };

            $data = $this->getUnfilteredPaymentsQuery()
                ->where('paid_at', '>=', now()->subMonths(12)->startOfMonth())
                ->selectRaw($selectRaw)
                ->groupByRaw($groupByRaw)
                ->orderByRaw("$groupByRaw asc")
                ->get();

            return $data->map(function ($item) {
                $parts = explode('-', $item->label_raw);
                $year = $parts[0];
                $month = (int) $parts[1];
                $monthName = Carbon::create()->month($month)->translatedFormat('F');

                return [
                    'label' => "{$monthName} {$year}",
                    'label_raw' => $item->label_raw,
                    'total_amount' => (float) $item->total_amount,
                    'count' => $item->transaction_count,
                ];
            })->all();
        }
    }

    #[Computed]
    public function trendTable()
    {
        $data = $this->trendData;
        $reversed = array_reverse($data);

        return array_slice($reversed, 0, 5);
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

        usort($rekap, fn ($a, $b) => $b['target'] <=> $a['target']);

        return $rekap;
    }

    #[Computed]
    public function rekapNonSpp()
    {
        if ($this->category === 'SPP') {
            return [];
        }

        $invoices = (clone $this->getFilteredInvoicesQuery())
            ->where('status', '!=', 'inactive')
            ->whereHas('feeType', fn ($q) => $q->where('category', '!=', 'SPP'))
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

        usort($rekap, fn ($a, $b) => $b['target'] <=> $a['target']);

    }

    #[Computed]
    public function sppTrendSummary()
    {
        $invoices = Invoice::where('status', '!=', 'inactive')
            ->whereHas('feeType', fn ($q) => $q->where('category', 'SPP'))
            ->get();

        $grouped = $invoices->groupBy(function ($invoice) {
            return $invoice->period_year.'-'.str_pad($invoice->period_month, 2, '0', STR_PAD_LEFT);
        });

        $rekap = [];
        foreach ($grouped as $period => $items) {
            if (! $items->first()->period_month) {
                continue;
            } // Skip if null

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
                'is_future' => $period > now()->format('Y-m'),
                'count_paid' => $countPaid,
                'count_total' => $countTotal,
            ];
        }

        usort($rekap, fn ($a, $b) => $b['sort_key'] <=> $a['sort_key']);

        return $rekap;
    }

    #[Computed]
    public function nonSppTrendSummary()
    {
        $invoices = Invoice::where('status', '!=', 'inactive')
            ->whereHas('feeType', fn ($q) => $q->where('category', '!=', 'SPP'))
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

        usort($rekap, fn ($a, $b) => $b['target'] <=> $a['target']);

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
            ->whereHas('feeType', fn ($q) => $q->where('category', 'SPP'))
            ->get();

        $grouped = $invoices->groupBy(function ($invoice) {
            return $invoice->period_year.'-'.str_pad($invoice->period_month, 2, '0', STR_PAD_LEFT);
        });

        $rekap = [];
        foreach ($grouped as $period => $items) {
            if (! $items->first()->period_month) {
                continue;
            } // Skip if null

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
                'is_future' => $period > now()->format('Y-m'),
                'count_paid' => $countPaid,
                'count_total' => $countTotal,
            ];
        }

        usort($rekap, fn ($a, $b) => $a['sort_key'] <=> $b['sort_key']);

        return $rekap;
    }

    protected function getPrintFilteredPaymentsQuery()
    {
        $query = Payment::whereBetween('paid_at', [$this->printStartDate.' 00:00:00', $this->printEndDate.' 23:59:59'])
            ->where(function ($q) {
                $q->where('status', 'success')->orWhereNull('status');
            });

        if ($this->printPaymentMethod !== 'all') {
            if ($this->printPaymentMethod === 'midtrans') {
                $query->where('method', '!=', 'manual')->where('method', '!=', 'cash');
            } else {
                $query->whereIn('method', ['manual', 'cash']);
            }
        }

        if ($this->printCategory !== 'all') {
            $query->whereHas('invoice.feeType', function ($q) {
                if ($this->printCategory === 'SPP') {
                    $q->where('category', 'SPP');
                } else {
                    $q->where('category', '!=', 'SPP');
                }
            });
        }

        if (! empty($this->printSearch)) {
            $query->where(function ($q) {
                $q->where('receipt_number', 'like', '%'.$this->printSearch.'%')
                    ->orWhereHas('invoice.student', function ($q2) {
                        $q2->where('name', 'like', '%'.$this->printSearch.'%')
                            ->orWhere('nis', 'like', '%'.$this->printSearch.'%');
                    });
            });
        }

        if ($this->printSelectedClass !== 'all') {
            if (str_starts_with($this->printSelectedClass, 'grade-')) {
                $grade = str_replace('grade-', '', $this->printSelectedClass);
                $query->whereHas('invoice.student.schoolClass', function ($q) use ($grade) {
                    $q->where('grade', $grade);
                });
            } elseif (str_starts_with($this->printSelectedClass, 'class-')) {
                $classId = str_replace('class-', '', $this->printSelectedClass);
                $query->whereHas('invoice.student', function ($q) use ($classId) {
                    $q->where('school_class_id', $classId);
                });
            }
        }

        return $query;
    }

    protected function getPrintFilteredInvoicesQuery()
    {
        $query = Invoice::whereDate('due_date', '>=', $this->printStartDate)
            ->whereDate('due_date', '<=', $this->printEndDate);

        if ($this->printCategory !== 'all') {
            $query->whereHas('feeType', function ($q) {
                if ($this->printCategory === 'SPP') {
                    $q->where('category', 'SPP');
                } else {
                    $q->where('category', '!=', 'SPP');
                }
            });
        }

        if ($this->printSelectedClass !== 'all') {
            if (str_starts_with($this->printSelectedClass, 'grade-')) {
                $grade = str_replace('grade-', '', $this->printSelectedClass);
                $query->whereHas('student.schoolClass', function ($q) use ($grade) {
                    $q->where('grade', $grade);
                });
            } elseif (str_starts_with($this->printSelectedClass, 'class-')) {
                $classId = str_replace('class-', '', $this->printSelectedClass);
                $query->whereHas('student', function ($q) use ($classId) {
                    $q->where('school_class_id', $classId);
                });
            }
        }

        return $query;
    }

    #[Computed]
    public function printKpi()
    {
        $totalInflow = (float) $this->getPrintFilteredPaymentsQuery()->sum('amount');
        $totalInflowCount = $this->getPrintFilteredPaymentsQuery()->count();

        $invoicesQuery = $this->getPrintFilteredInvoicesQuery();
        $totalNewDebt = (float) (clone $invoicesQuery)->where('status', 'unpaid')->sum('amount');
        $totalNewDebtCount = (clone $invoicesQuery)->where('status', 'unpaid')->count();

        $totalInvoicesCount = (clone $invoicesQuery)->where('status', '!=', 'inactive')->count();
        $paidInvoicesCount = (clone $invoicesQuery)->where('status', 'paid')->count();
        $collectionRate = $totalInvoicesCount > 0 ? round(($paidInvoicesCount / $totalInvoicesCount) * 100, 1) : 0;

        return [
            'totalInflow' => $totalInflow,
            'totalInflowCount' => $totalInflowCount,
            'totalNewDebt' => $totalNewDebt,
            'totalNewDebtCount' => $totalNewDebtCount,
            'collectionRate' => $collectionRate,
        ];
    }

    #[Computed]
    public function printBreakdown()
    {
        $invoices = $this->getPrintFilteredInvoicesQuery()
            ->where('status', '!=', 'inactive')
            ->with('feeType')
            ->get();

        $grouped = $invoices->groupBy(function ($inv) {
            return $inv->feeType ? $inv->feeType->name : 'Lainnya';
        });

        $breakdown = [];
        foreach ($grouped as $categoryName => $items) {
            $catTarget = $items->sum('amount');
            $catPaid = $items->where('status', 'paid')->sum('amount');
            $catUnpaid = $items->where('status', 'unpaid')->sum('amount');
            $catRate = $catTarget > 0 ? round(($catPaid / $catTarget) * 100, 1) : 0;

            $breakdown[] = [
                'category' => $categoryName,
                'target' => $catTarget,
                'paid' => $catPaid,
                'unpaid' => $catUnpaid,
                'rate' => $catRate,
            ];
        }

        usort($breakdown, fn ($a, $b) => $b['target'] <=> $a['target']);

        return $breakdown;
    }

    #[Computed]
    public function printPayments()
    {
        return $this->getPrintFilteredPaymentsQuery()
            ->with(['invoice.student', 'invoice.feeType', 'recorder'])
            ->orderBy('paid_at', 'asc')
            ->get();
    }

    #[Computed]
    public function classFilterOptions()
    {
        $grades = SchoolClass::pluck('grade')->unique()->sort()->values();
        $options = [];

        foreach ($grades as $grade) {
            $options[] = [
                'value' => 'grade-'.$grade,
                'label' => 'Semua Kelas '.$grade,
                'is_grade' => true,
            ];

            $classes = SchoolClass::where('grade', $grade)->orderBy('name')->get();
            foreach ($classes as $class) {
                $options[] = [
                    'value' => 'class-'.$class->id,
                    'label' => $class->name,
                    'is_grade' => false,
                ];
            }
        }

        return $options;
    }

    public function resetFilters()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');
        $this->category = 'all';
        $this->paymentMethod = 'all';
        $this->search = '';
        $this->selectedClass = 'all';

        $this->resetPage('ledgerPage');
    }

    #[Computed]
    public function ledger()
    {
        return $this->getFilteredPaymentsQuery()
            ->with(['invoice.student', 'invoice.feeType', 'recorder'])
            ->orderBy('paid_at', 'desc')
            ->paginate(6, ['*'], 'ledgerPage');
    }

    public function exportPdf()
    {
        return redirect()->route('finance.reports.financial.pdf', [
            'start' => $this->startDate,
            'end' => $this->endDate,
            'category' => $this->category,
            'method' => $this->paymentMethod,
            'search' => $this->search,
            'class' => $this->selectedClass,
        ]);
    }

    public function render()
    {
        return view('livewire.pages.finance.financial-report')->title(__('Laporan Keuangan'));
    }
}

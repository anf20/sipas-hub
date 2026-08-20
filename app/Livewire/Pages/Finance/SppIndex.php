<?php

namespace App\Livewire\Pages\Finance;

use App\Jobs\GenerateYearlyInvoiceJob;
use App\Models\FeeType;
use App\Models\Invoice;
use App\Models\Student;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class SppIndex extends Component
{
    use WithPagination;

    public $month;

    public $year;

    public $default_amount = 0;

    public $due_date;

    public $viewMode = 'current_month';

    public $adjust_amount = 0;

    public function mount()
    {
        $this->month = (int) date('n');
        $this->year = (int) date('Y');
        $this->due_date = date('Y-m-d', strtotime('+10 days'));

        $latestSpp = FeeType::where('category', 'SPP')->latest()->first();
        if ($latestSpp) {
            $this->adjust_amount = $latestSpp->default_amount;
        }
    }

    public function adjustSppAmount()
    {
        $this->validate([
            'adjust_amount' => 'required|numeric|min:0',
        ]);

        $latestSpp = FeeType::where('category', 'SPP')->latest()->first();
        if (! $latestSpp) {
            \Flux::toast(__('Tidak ada catatan SPP yang aktif.'), variant: 'danger');

            return;
        }

        // 1. Update the default amount on the FeeType so future students get the new amount
        $latestSpp->update(['default_amount' => $this->adjust_amount]);

        // 2. Update all unpaid and inactive invoices for this FeeType
        $updatedCount = Invoice::where('fee_type_id', $latestSpp->id)
            ->whereIn('status', ['unpaid', 'inactive'])
            ->update(['amount' => $this->adjust_amount]);

        \Flux::toast(__('Berhasil menyesuaikan nominal SPP menjadi Rp '.number_format($this->adjust_amount, 0, ',', '.')." untuk {$updatedCount} tagihan yang belum lunas."), variant: 'success');

        $this->dispatch('close-modal', 'adjust-spp-modal');
    }

    public function generateSpp()
    {
        $this->validate([
            'year' => 'required|integer',
            'default_amount' => 'required|numeric|min:0',
        ]);

        $startYear = (int) $this->year;
        $endYear = $startYear + 1;
        $name = "SPP Tahun Ajaran {$startYear}/{$endYear}";

        $isAlreadyGenerated = FeeType::where('category', 'SPP')
            ->where('name', $name)
            ->exists();

        if ($isAlreadyGenerated) {
            \Flux::toast(__("SPP untuk {$name} sudah dibuat sebelumnya!"), variant: 'danger');

            return;
        }

        // 1. Create the billing event record
        $feeType = FeeType::create([
            'name' => $name,
            'category' => 'SPP',
            'default_amount' => $this->default_amount,
            'is_recurring' => true,
            'recurrence' => 'tahunan', // Set to tahunan for academic year
            'applicable_grades' => null, // all
            'is_active' => true,
        ]);

        $activeStudents = Student::where('status', 'aktif')->pluck('id');
        $adminId = auth()->id();

        foreach ($activeStudents as $studentId) {
            GenerateYearlyInvoiceJob::dispatch(
                $studentId,
                $feeType->id,
                $this->default_amount,
                7, // Mulai dari Juli
                $startYear,
                $adminId
            );
        }

        \Flux::toast(__('Proses background pembuatan SPP 1 Tahun untuk '.$activeStudents->count().' siswa sedang berjalan!'), variant: 'success');

        // Reset inputs after success
        $this->year = (int) date('Y');

        $this->dispatch('close-modal', 'generate-spp-modal');
    }

    public function delete($id)
    {
        $feeType = FeeType::findOrFail($id);
        $feeType->delete();
        \Flux::toast(__('Catatan SPP berhasil dihapus.'), variant: 'success');
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

        $invoiceQuery = Invoice::whereHas('feeType', function ($q) {
            $q->where('category', 'SPP');
        })
            ->where(function ($q) use ($activeStartYear, $activeEndYear) {
                $q->where(function ($sub) use ($activeStartYear) {
                    $sub->where('period_year', $activeStartYear)->where('period_month', '>=', 7);
                })->orWhere(function ($sub) use ($activeEndYear) {
                    $sub->where('period_year', $activeEndYear)->where('period_month', '<=', 6);
                });
            });

        if ($this->viewMode === 'current_month') {
            $invoiceQuery->where(function ($q) {
                $currentYear = (int) date('Y');
                $currentMonth = (int) date('n');
                $q->where('period_year', '<', $currentYear)
                    ->orWhere(function ($sub) use ($currentYear, $currentMonth) {
                        $sub->where('period_year', $currentYear)->where('period_month', '<=', $currentMonth);
                    });
            })->where('status', '!=', 'inactive');
        }

        $sppMonthlyTable = $invoiceQuery
            ->selectRaw("
            period_year,
            period_month,
            SUM(CASE WHEN status = 'paid' THEN amount ELSE 0 END) as total_paid,
            SUM(CASE WHEN status = 'unpaid' THEN amount ELSE 0 END) as total_unpaid,
            SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as count_paid,
            COUNT(id) as count_total
        ")
            ->groupBy('period_year', 'period_month')
            ->orderBy('period_year', 'asc')
            ->orderBy('period_month', 'asc')
            ->paginate(12, ['*'], 'sppPage');

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

        $sppTotalInvoiced = (float) (clone $sppCurrentMonthQuery)->sum('amount');
        $sppTotalInvoicedCount = (clone $sppCurrentMonthQuery)->count();
        $sppTotalPaid = (float) (clone $sppCurrentMonthQuery)->where('status', 'paid')->sum('amount');
        $sppTotalPaidCount = (clone $sppCurrentMonthQuery)->where('status', 'paid')->count();
        $sppTotalUnpaid = (float) (clone $sppCurrentMonthQuery)->where('status', 'unpaid')->sum('amount');
        $sppTotalUnpaidCount = (clone $sppCurrentMonthQuery)->where('status', 'unpaid')->count();

        $sppLastMonthInvoiced = (float) (clone $sppLastMonthQuery)->sum('amount');
        $sppLastMonthInvoicedCount = (clone $sppLastMonthQuery)->count();
        $sppLastMonthPaid = (float) (clone $sppLastMonthQuery)->where('status', 'paid')->sum('amount');
        $sppLastMonthPaidCount = (clone $sppLastMonthQuery)->where('status', 'paid')->count();
        $sppLastMonthUnpaid = (float) (clone $sppLastMonthQuery)->where('status', 'unpaid')->sum('amount');
        $sppLastMonthUnpaidCount = (clone $sppLastMonthQuery)->where('status', 'unpaid')->count();

        $isCurrentYearGenerated = FeeType::where('category', 'SPP')
            ->where('name', "SPP Tahun Ajaran {$activeStartYear}/{$activeEndYear}")
            ->exists();

        return view('livewire.pages.finance.spp-index', [
            'activeAcademicYearName' => "Tahun Ajaran {$activeStartYear}/{$activeEndYear}",
            'isCurrentYearGenerated' => $isCurrentYearGenerated,
            'sppBatches' => FeeType::where('category', 'SPP')->latest()->get(),
            'sppMonthlyTable' => $sppMonthlyTable,
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
            'months' => [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
            ],
        ])->title(__('Manajemen SPP Bulanan'));
    }
}

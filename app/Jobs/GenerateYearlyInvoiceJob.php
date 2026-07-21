<?php

namespace App\Jobs;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateYearlyInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $studentId;

    protected $feeTypeId;

    protected $amount;

    protected $startMonth;

    protected $startYear;

    protected $adminId;

    /**
     * Create a new job instance.
     */
    public function __construct($studentId, $feeTypeId, $amount, $startMonth, $startYear, $adminId)
    {
        $this->studentId = $studentId;
        $this->feeTypeId = $feeTypeId;
        $this->amount = $amount;
        $this->startMonth = $startMonth;
        $this->startYear = $startYear;
        $this->adminId = $adminId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $currentDate = Carbon::now();
        $currentMonth = $currentDate->month;
        $currentYear = $currentDate->year;

        $dateIterator = Carbon::createFromDate($this->startYear, $this->startMonth, 1);

        for ($i = 0; $i < 12; $i++) {
            $periodMonth = $dateIterator->month;
            $periodYear = $dateIterator->year;

            // Jika periode invoice sama dengan atau sebelum bulan saat ini, set unpaid
            // Jika di masa depan, set inactive
            $isFuture = ($periodYear > $currentYear) ||
                        ($periodYear == $currentYear && $periodMonth > $currentMonth);

            $status = $isFuture ? 'inactive' : 'unpaid';

            try {
                // Menggunakan firstOrCreate untuk menghindari duplikat
                // karena ada composite unique index unique_monthly_invoice
                Invoice::firstOrCreate(
                    [
                        'student_id' => $this->studentId,
                        'fee_type_id' => $this->feeTypeId,
                        'period_month' => $periodMonth,
                        'period_year' => $periodYear,
                    ],
                    [
                        'amount' => $this->amount,
                        'due_date' => $dateIterator->copy()->endOfMonth(),
                        'status' => $status,
                        'generated_by' => $this->adminId,
                        'snap_token' => null,
                        'notes' => 'Generated automatically by system',
                    ]
                );
            } catch (\Exception $e) {
                Log::error("Failed generating invoice for student {$this->studentId} period {$periodMonth}/{$periodYear}: ".$e->getMessage());
            }

            $dateIterator->addMonth();
        }
    }
}

<?php

namespace App\Jobs;

use App\Models\FeeType;
use App\Models\Invoice;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateInvoices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param  array  $targetConfig  ['type' => 'all|grade|class|student', 'value' => mixed]
     */
    public function __construct(
        public int $feeTypeId,
        public int $month,
        public int $year,
        public string $dueDate,
        public array $targetConfig = ['type' => 'all'],
        public ?int $generatedBy = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $feeType = FeeType::findOrFail($this->feeTypeId);

        $query = Student::where('status', 'aktif');

        $type = $this->targetConfig['type'] ?? 'all';
        $value = $this->targetConfig['value'] ?? null;

        // Apply targeting logic based on the user selection from the form
        if ($type === 'grade' && $value) {
            $query->whereHas('schoolClass', function ($q) use ($value) {
                if (is_array($value)) {
                    $q->whereIn('grade', $value);
                } else {
                    $q->where('grade', $value);
                }
            });
        } elseif ($type === 'class' && $value) {
            $query->where('school_class_id', $value);
        } elseif ($type === 'student' && is_array($value)) {
            $query->whereIn('id', $value);
        }

        $students = $query->get();
        $successCount = 0;
        $skipCount = 0;

        // Ensure periods are integers for strict database comparison
        $month = (int) $this->month;
        $year = (int) $this->year;

        foreach ($students as $student) {
            try {
                // Check if already exists to prevent duplication
                $exists = Invoice::where([
                    'student_id' => $student->id,
                    'fee_type_id' => $feeType->id,
                    'period_month' => $month,
                    'period_year' => $year,
                ])->exists();

                if ($exists) {
                    $skipCount++;

                    continue;
                }

                Invoice::create([
                    'student_id' => $student->id,
                    'fee_type_id' => $feeType->id,
                    'amount' => $feeType->default_amount,
                    'due_date' => $this->dueDate,
                    'period_month' => $month,
                    'period_year' => $year,
                    'status' => 'unpaid',
                    'generated_by' => $this->generatedBy,
                ]);

                $successCount++;
            } catch (\Exception $e) {
                Log::error("Failed to generate Invoice for student ID {$student->id} (FeeType #{$this->feeTypeId}): ".$e->getMessage());
            }
        }

        Log::info("Invoice Generation Complete for FeeType #{$this->feeTypeId}. Success: $successCount, Skipped: $skipCount");
    }
}

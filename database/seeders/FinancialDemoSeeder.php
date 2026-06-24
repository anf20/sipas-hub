<?php

namespace Database\Seeders;

use App\Models\FeeType;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FinancialDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::role('Super Admin')->first();
        $feeType = FeeType::where('name', 'SPP Bulanan')->first();
        $students = Student::inRandomOrder()->take(20)->get();

        if (!$feeType || $students->isEmpty()) {
            return;
        }

        $this->command->info("Membuat tagihan historis untuk " . $students->count() . " siswa acak...");

        $monthsAgo = [3, 2, 1]; // 3 bulan lalu, 2 bulan lalu, 1 bulan lalu

        foreach ($students as $student) {
            foreach ($monthsAgo as $index => $months) {
                $pastDate = Carbon::now()->subMonths($months)->startOfMonth();
                $dueDate = $pastDate->copy()->addDays(9); // Jatuh tempo tanggal 10 tiap bulan

                // Buat Tagihan
                $invoice = Invoice::create([
                    'student_id' => $student->id,
                    'fee_type_id' => $feeType->id,
                    'amount' => $feeType->default_amount,
                    'due_date' => $dueDate,
                    'period_month' => $pastDate->month,
                    'period_year' => $pastDate->year,
                    'status' => 'unpaid', // Default
                    'generated_by' => $admin ? $admin->id : null,
                ]);

                // Simulasi Pembayaran: Lunas untuk bulan-bulan awal, dan biarkan beberapa bulan terakhir menunggak
                // Misal: tagihan 3 bulan lalu lunas, 2 bulan lalu lunas, 1 bulan lalu biarkan unpaid
                if ($index < 2) {
                    $paidDate = $pastDate->copy()->addDays(rand(2, 8)); // Dibayar antara tgl 3-9
                    
                    Payment::create([
                        'invoice_id' => $invoice->id,
                        'amount' => $invoice->amount,
                        'method' => 'manual',
                        'status' => 'settlement',
                        'paid_at' => $paidDate,
                        'receipt_number' => 'REC-' . strtoupper(uniqid()),
                        'recorded_by' => $admin ? $admin->id : null,
                    ]);

                    $invoice->update(['status' => 'paid']);
                }
            }
        }
    }
}

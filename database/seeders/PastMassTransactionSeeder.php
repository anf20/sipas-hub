<?php

namespace Database\Seeders;

use App\Models\FeeType;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PastMassTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Memulai generasi transaksi masal tahun lalu (2025)...');

        $students = Student::all();
        if ($students->isEmpty()) {
            $this->command->warn('Tidak ada siswa aktif ditemukan. Lewati seeder ini.');
            return;
        }

        $sppFee = FeeType::where('category', 'SPP')->first();
        if (!$sppFee) {
            $sppFee = FeeType::create([
                'name' => 'SPP Bulanan (Historical)',
                'category' => 'SPP',
                'default_amount' => 300000,
                'is_active' => true,
            ]);
        }

        $otherFee = FeeType::where('category', '!=', 'SPP')->first();
        
        $year = 2025;
        $months = range(1, 12);
        
        $paymentMethods = ['midtrans_qris', 'midtrans_bank_transfer', 'cash', 'transfer'];

        foreach ($months as $month) {
            $this->command->info("Generate transaksi bulan $month/$year...");
            
            foreach ($students as $student) {
                // Generate SPP Invoice
                $invoice = Invoice::create([
                    'student_id' => $student->id,
                    'fee_type_id' => $sppFee->id,
                    'amount' => $sppFee->default_amount,
                    'due_date' => Carbon::create($year, $month, 10)->format('Y-m-d'), // Tanggal 10 tiap bulan
                    'period_month' => $month,
                    'period_year' => $year,
                    'status' => 'unpaid',
                    'notes' => "SPP Bulan $month Tahun $year",
                ]);

                // 85% chance of being paid
                if (rand(1, 100) <= 85) {
                    $invoice->update(['status' => 'paid']);
                    
                    // Random paid date between 1st and 20th of the month
                    $paidDate = Carbon::create($year, $month, rand(1, 20), rand(8, 16), rand(0, 59));
                    
                    Payment::create([
                        'invoice_id' => $invoice->id,
                        'amount' => $invoice->amount,
                        'method' => $paymentMethods[array_rand($paymentMethods)],
                        'paid_at' => $paidDate,
                        'receipt_number' => 'INV-SPP-' . $year . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . strtoupper(Str::random(6)),
                        'status' => 'success',
                        'created_at' => $paidDate,
                        'updated_at' => $paidDate,
                    ]);
                }
                
                // Optional: Randomly add other fees (30% chance in random months)
                if ($otherFee && rand(1, 100) <= 10) {
                    $otherInv = Invoice::create([
                        'student_id' => $student->id,
                        'fee_type_id' => $otherFee->id,
                        'amount' => $otherFee->default_amount,
                        'due_date' => Carbon::create($year, $month, 25)->format('Y-m-d'),
                        'status' => 'unpaid',
                        'notes' => "Tagihan Tambahan Tahun $year",
                    ]);

                    if (rand(1, 100) <= 90) { // High payment rate for other fees
                        $otherInv->update(['status' => 'paid']);
                        $paidDateOther = Carbon::create($year, $month, rand(20, 28), rand(8, 16), rand(0, 59));
                        
                        Payment::create([
                            'invoice_id' => $otherInv->id,
                            'amount' => $otherInv->amount,
                            'method' => $paymentMethods[array_rand($paymentMethods)],
                            'paid_at' => $paidDateOther,
                            'receipt_number' => 'INV-OTH-' . $year . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . strtoupper(Str::random(6)),
                            'status' => 'success',
                            'created_at' => $paidDateOther,
                            'updated_at' => $paidDateOther,
                        ]);
                    }
                }
            }
        }
        
        $this->command->info('Selesai generate transaksi tahun lalu!');
    }
}

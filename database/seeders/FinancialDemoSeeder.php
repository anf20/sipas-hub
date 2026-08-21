<?php

namespace Database\Seeders;

use App\Models\FeeType;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FinancialDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::role('Super Admin')->first();
        $adminId = $admin ? $admin->id : 1;

        $this->command->info('Membersihkan data transaksi dan pembayaran lama...');
        Payment::query()->forceDelete();
        Invoice::query()->forceDelete();

        // 1. Setup Master Jenis Tagihan
        $sppType = FeeType::firstOrCreate(
            ['name' => 'SPP Bulanan'],
            [
                'category' => 'SPP',
                'default_amount' => 350000,
                'is_recurring' => true,
                'recurrence' => 'bulanan',
                'is_active' => true,
            ]
        );

        $pangkalType = FeeType::firstOrCreate(
            ['name' => 'Uang Pangkal / Pendaftaran'],
            [
                'category' => 'lain',
                'default_amount' => 1500000,
                'is_recurring' => false,
                'recurrence' => 'sekali',
                'is_active' => true,
            ]
        );

        $seragamType = FeeType::firstOrCreate(
            ['name' => 'Uang Seragam Santri'],
            [
                'category' => 'lain',
                'default_amount' => 600000,
                'is_recurring' => false,
                'recurrence' => 'sekali',
                'is_active' => true,
            ]
        );

        $bukuType = FeeType::firstOrCreate(
            ['name' => 'Uang Kitab & Buku Pelajaran'],
            [
                'category' => 'lain',
                'default_amount' => 450000,
                'is_recurring' => false,
                'recurrence' => 'sekali',
                'is_active' => true,
            ]
        );

        $kegiatanType = FeeType::firstOrCreate(
            ['name' => 'Kegiatan Santri & Ekstrakurikuler'],
            [
                'category' => 'kegiatan',
                'default_amount' => 150000,
                'is_recurring' => false,
                'recurrence' => 'sekali',
                'is_active' => true,
            ]
        );

        $students = Student::all();
        if ($students->isEmpty()) {
            $this->command->error('Tidak ada santri ditemukan. Jalankan StudentSeeder terlebih dahulu.');

            return;
        }

        $paymentMethods = ['midtrans_qris', 'midtrans_bank_transfer', 'cash', 'transfer'];

        // =========================================================================
        // TAHUN AJARAN LALU (2024/2025): Juli 2024 s/d Juni 2025
        // Total 12 Bulan SPP + Tagihan Awal Tahun
        // Syarat: Sisakan TUNGGAKAN 25% dari total tertagih (75% LUNAS, 25% BELUM LUNAS)
        // =========================================================================
        $this->command->info('Membuat data transaksi Tahun Ajaran Lalu (2024/2025) dengan tunggakan 25%...');

        $pastMonths = [
            ['month' => 7, 'year' => 2024],
            ['month' => 8, 'year' => 2024],
            ['month' => 9, 'year' => 2024],
            ['month' => 10, 'year' => 2024],
            ['month' => 11, 'year' => 2024],
            ['month' => 12, 'year' => 2024],
            ['month' => 1, 'year' => 2025],
            ['month' => 2, 'year' => 2025],
            ['month' => 3, 'year' => 2025],
            ['month' => 4, 'year' => 2025],
            ['month' => 5, 'year' => 2025],
            ['month' => 6, 'year' => 2025],
        ];

        foreach ($students as $studentIndex => $student) {
            // A. Tagihan SPP 12 Bulan Tahun Lalu
            foreach ($pastMonths as $mIdx => $p) {
                $month = $p['month'];
                $year = $p['year'];
                $dueDate = Carbon::create($year, $month, 10);

                // Buat Invoice SPP
                $invoice = Invoice::create([
                    'student_id' => $student->id,
                    'fee_type_id' => $sppType->id,
                    'amount' => $sppType->default_amount,
                    'due_date' => $dueDate,
                    'period_month' => $month,
                    'period_year' => $year,
                    'status' => 'unpaid',
                    'notes' => "SPP Bulan $month/$year",
                    'generated_by' => $adminId,
                ]);

                // Target: 75% Lunas, 25% Tunggakan
                // Gunakan formula deterministik agar tepat 75% lunas
                $isPaid = (($studentIndex * 13 + $mIdx) % 100) < 75;

                if ($isPaid) {
                    $invoice->update(['status' => 'paid']);
                    $paidDate = Carbon::create($year, $month, rand(1, 15), rand(8, 16), rand(0, 59));
                    $method = $paymentMethods[array_rand($paymentMethods)];

                    Payment::create([
                        'invoice_id' => $invoice->id,
                        'amount' => $invoice->amount,
                        'method' => $method,
                        'status' => 'success',
                        'paid_at' => $paidDate,
                        'receipt_number' => 'INV-SPP-'.$year.str_pad($month, 2, '0', STR_PAD_LEFT).'-'.strtoupper(Str::random(6)),
                        'recorded_by' => $adminId,
                        'created_at' => $paidDate,
                        'updated_at' => $paidDate,
                    ]);
                }
            }

            // B. Tagihan Awal Tahun Non-SPP (Seragam & Kitab) Tahun Lalu
            $seragamInv = Invoice::create([
                'student_id' => $student->id,
                'fee_type_id' => $seragamType->id,
                'amount' => $seragamType->default_amount,
                'due_date' => Carbon::create(2024, 7, 25),
                'period_month' => 7,
                'period_year' => 2024,
                'status' => 'unpaid',
                'notes' => 'Uang Seragam Santri 2024/2025',
                'generated_by' => $adminId,
            ]);

            $bukuInv = Invoice::create([
                'student_id' => $student->id,
                'fee_type_id' => $bukuType->id,
                'amount' => $bukuType->default_amount,
                'due_date' => Carbon::create(2024, 8, 20),
                'period_month' => 8,
                'period_year' => 2024,
                'status' => 'unpaid',
                'notes' => 'Uang Kitab & Buku 2024/2025',
                'generated_by' => $adminId,
            ]);

            // Non-SPP juga mengikuti rasio 75% lunas, 25% nunggak
            if (($studentIndex % 4) !== 0) { // 75% lunas
                $seragamInv->update(['status' => 'paid']);
                $pDate = Carbon::create(2024, 7, rand(15, 28), rand(8, 16), rand(0, 59));
                Payment::create([
                    'invoice_id' => $seragamInv->id,
                    'amount' => $seragamInv->amount,
                    'method' => $paymentMethods[array_rand($paymentMethods)],
                    'status' => 'success',
                    'paid_at' => $pDate,
                    'receipt_number' => 'INV-SRG-202407-'.strtoupper(Str::random(6)),
                    'recorded_by' => $adminId,
                    'created_at' => $pDate,
                    'updated_at' => $pDate,
                ]);
            }

            if (($studentIndex % 4) !== 1) { // 75% lunas
                $bukuInv->update(['status' => 'paid']);
                $pDate = Carbon::create(2024, 8, rand(10, 25), rand(8, 16), rand(0, 59));
                Payment::create([
                    'invoice_id' => $bukuInv->id,
                    'amount' => $bukuInv->amount,
                    'method' => $paymentMethods[array_rand($paymentMethods)],
                    'status' => 'success',
                    'paid_at' => $pDate,
                    'receipt_number' => 'INV-BKU-202408-'.strtoupper(Str::random(6)),
                    'recorded_by' => $adminId,
                    'created_at' => $pDate,
                    'updated_at' => $pDate,
                ]);
            }
        }

        // =========================================================================
        // TAHUN AJARAN SEKARANG (2025/2026): Juli 2025 s/d Februari 2026
        // Menampilkan data operasional kasir & tagihan berjalan
        // =========================================================================
        $this->command->info('Membuat data transaksi Tahun Ajaran Berjalan (2025/2026)...');

        $currentMonths = [
            ['month' => 7, 'year' => 2025],
            ['month' => 8, 'year' => 2025],
            ['month' => 9, 'year' => 2025],
            ['month' => 10, 'year' => 2025],
            ['month' => 11, 'year' => 2025],
            ['month' => 12, 'year' => 2025],
            ['month' => 1, 'year' => 2026],
            ['month' => 2, 'year' => 2026],
        ];

        foreach ($students as $studentIndex => $student) {
            foreach ($currentMonths as $mIdx => $c) {
                $month = $c['month'];
                $year = $c['year'];
                $dueDate = Carbon::create($year, $month, 10);

                $invoice = Invoice::create([
                    'student_id' => $student->id,
                    'fee_type_id' => $sppType->id,
                    'amount' => $sppType->default_amount,
                    'due_date' => $dueDate,
                    'period_month' => $month,
                    'period_year' => $year,
                    'status' => 'unpaid',
                    'notes' => "SPP Bulan $month/$year",
                    'generated_by' => $adminId,
                ]);

                // Pola bayar realistis: bulan awal (Juli-Desember) 80% lunas, bulan akhir (Januari-Februari) 60% lunas
                $payRate = ($month <= 12 && $year == 2025) ? 80 : 60;
                $isPaid = (($studentIndex * 7 + $mIdx * 11) % 100) < $payRate;

                if ($isPaid) {
                    $invoice->update(['status' => 'paid']);
                    $paidDate = Carbon::create($year, $month, rand(1, 12), rand(8, 16), rand(0, 59));
                    $method = $paymentMethods[array_rand($paymentMethods)];

                    Payment::create([
                        'invoice_id' => $invoice->id,
                        'amount' => $invoice->amount,
                        'method' => $method,
                        'status' => 'success',
                        'paid_at' => $paidDate,
                        'receipt_number' => 'INV-SPP-'.$year.str_pad($month, 2, '0', STR_PAD_LEFT).'-'.strtoupper(Str::random(6)),
                        'recorded_by' => $adminId,
                        'created_at' => $paidDate,
                        'updated_at' => $paidDate,
                    ]);
                }
            }

            // Tagihan Kegiatan Semester Ini
            $kegiatanInv = Invoice::create([
                'student_id' => $student->id,
                'fee_type_id' => $kegiatanType->id,
                'amount' => $kegiatanType->default_amount,
                'due_date' => Carbon::create(2025, 9, 15),
                'period_month' => 9,
                'period_year' => 2025,
                'status' => 'unpaid',
                'notes' => 'Kegiatan Santri Semester Ganjil 2025',
                'generated_by' => $adminId,
            ]);

            if ($studentIndex % 3 !== 0) { // 66% lunas
                $kegiatanInv->update(['status' => 'paid']);
                $pDate = Carbon::create(2025, 9, rand(5, 20), rand(8, 16), rand(0, 59));
                Payment::create([
                    'invoice_id' => $kegiatanInv->id,
                    'amount' => $kegiatanInv->amount,
                    'method' => $paymentMethods[array_rand($paymentMethods)],
                    'status' => 'success',
                    'paid_at' => $pDate,
                    'receipt_number' => 'INV-KGT-202509-'.strtoupper(Str::random(6)),
                    'recorded_by' => $adminId,
                    'created_at' => $pDate,
                    'updated_at' => $pDate,
                ]);
            }
        }

        // =========================================================================
        // SIMULASI 3 TRANSAKSI MENUNGGU VERIFIKASI (Untuk Demo Menu Verifikasi)
        // =========================================================================
        $unpaidInvoices = Invoice::where('status', 'unpaid')->limit(3)->get();
        foreach ($unpaidInvoices as $idx => $inv) {
            Payment::create([
                'invoice_id' => $inv->id,
                'amount' => $inv->amount,
                'method' => 'transfer',
                'status' => 'pending',
                'proof_file' => 'proofs/dummy_receipt.jpg',
                'paid_at' => now()->subDays($idx + 1),
                'receipt_number' => 'VERIF-'.date('Ymd').'-'.strtoupper(Str::random(5)),
                'recorded_by' => null,
            ]);
        }

        $this->command->info('Seeding seluruh data keuangan dan transaksi historis selesai dengan sukses!');
    }
}

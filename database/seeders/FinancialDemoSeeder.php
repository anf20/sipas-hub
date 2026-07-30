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

        // 1. Bersihkan data transaksi lama (Invoices & Payments)
        $this->command->info('Membersihkan data transaksi dan pembayaran lama...');
        Payment::query()->forceDelete();
        Invoice::query()->forceDelete();

        // 2. Pastikan Kategori Tagihan Semester Baru Tersedia
        $pramukaType = FeeType::firstOrCreate(
            ['name' => 'Kegiatan Pramuka'],
            [
                'category' => 'kegiatan',
                'default_amount' => 50000,
                'is_recurring' => false,
                'recurrence' => 'sekali',
                'is_active' => true,
            ]
        );

        $bukuType = FeeType::firstOrCreate(
            ['name' => 'Uang Buku & Perlengkapan'],
            [
                'category' => 'lain',
                'default_amount' => 450000,
                'is_recurring' => false,
                'recurrence' => 'sekali',
                'is_active' => true,
            ]
        );

        $seragamType = FeeType::firstOrCreate(
            ['name' => 'Uang Seragam Pondok'],
            [
                'category' => 'lain',
                'default_amount' => 600000,
                'is_recurring' => false,
                'recurrence' => 'sekali',
                'is_active' => true,
            ]
        );

        $kesehatanType = FeeType::firstOrCreate(
            ['name' => 'Iuran Kesehatan & Kebersihan'],
            [
                'category' => 'lain',
                'default_amount' => 75000,
                'is_recurring' => false,
                'recurrence' => 'sekali',
                'is_active' => true,
            ]
        );

        // 3. Dapatkan seluruh 100 santri aktif
        $students = Student::all();

        if ($students->isEmpty()) {
            $this->command->error('Tidak ada data santri ditemukan. Jalankan StudentSeeder terlebih dahulu.');

            return;
        }

        $this->command->info('Membuat tagihan iuran awal semester (Non-SPP) untuk '.$students->count().' santri...');

        // Tahun ajaran aktif 2025/2026 dimulai bulan Juli 2025
        $july2025 = Carbon::create(2025, 7, 1, 0, 0, 0);

        foreach ($students as $student) {
            // A. Tagihan Kegiatan Pramuka (Jatuh tempo 15 Juli 2025)
            Invoice::create([
                'student_id' => $student->id,
                'fee_type_id' => $pramukaType->id,
                'amount' => $pramukaType->default_amount,
                'due_date' => $july2025->copy()->addDays(14), // 15 Juli 2025
                'period_month' => 7,
                'period_year' => 2025,
                'status' => 'unpaid',
                'generated_by' => $admin ? $admin->id : null,
            ]);

            // B. Tagihan Uang Buku & Perlengkapan (Jatuh tempo 20 Juli 2025)
            Invoice::create([
                'student_id' => $student->id,
                'fee_type_id' => $bukuType->id,
                'amount' => $bukuType->default_amount,
                'due_date' => $july2025->copy()->addDays(19), // 20 Juli 2025
                'period_month' => 7,
                'period_year' => 2025,
                'status' => 'unpaid',
                'generated_by' => $admin ? $admin->id : null,
            ]);

            // C. Tagihan Uang Seragam Pondok (Jatuh tempo 25 Juli 2025)
            Invoice::create([
                'student_id' => $student->id,
                'fee_type_id' => $seragamType->id,
                'amount' => $seragamType->default_amount,
                'due_date' => $july2025->copy()->addDays(24), // 25 Juli 2025
                'period_month' => 7,
                'period_year' => 2025,
                'status' => 'unpaid',
                'generated_by' => $admin ? $admin->id : null,
            ]);

            // D. Tagihan Iuran Kesehatan & Kebersihan (Jatuh tempo 30 Juli 2025)
            Invoice::create([
                'student_id' => $student->id,
                'fee_type_id' => $kesehatanType->id,
                'amount' => $kesehatanType->default_amount,
                'due_date' => $july2025->copy()->addDays(29), // 30 Juli 2025
                'period_month' => 7,
                'period_year' => 2025,
                'status' => 'unpaid',
                'generated_by' => $admin ? $admin->id : null,
            ]);
        }

        $this->command->info('Berhasil membuat total '.($students->count() * 4).' tagihan awal semester non-SPP!');
    }
}

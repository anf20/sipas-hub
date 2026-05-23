<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\FeeType;
use App\Models\Invoice;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class TestDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database for testing.
     */
    public function run(): void
    {
        // 1. Create Roles
        $roles = ['Super Admin', 'Orang Tua'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // 2. Create Super Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('Super Admin');

        // 3. Create Basic Infrastructure
        $academicYear = AcademicYear::firstOrCreate([
            'name' => '2025/2026',
        ], [
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
            'is_active' => true,
        ]);

        $class = SchoolClass::firstOrCreate([
            'name' => 'Kelas 7A',
            'academic_year_id' => $academicYear->id,
        ], [
            'grade' => '7',
        ]);

        // 4. Create Fee Types
        $sppFee = FeeType::firstOrCreate(
            ['name' => 'SPP Bulanan'],
            [
                'category' => 'SPP',
                'default_amount' => 250000,
                'is_recurring' => true,
                'recurrence' => 'bulanan',
                'applicable_grades' => json_encode(['7', '8', '9']),
                'is_active' => true,
            ]
        );

        // 5. Create Parent and Students
        $parent = User::firstOrCreate(
            ['email' => 'wali@test.com'],
            [
                'name' => 'Wali Murid Test',
                'password' => Hash::make('password'),
            ]
        );
        $parent->assignRole('Orang Tua');

        $student1 = Student::firstOrCreate(
            ['nisn' => '1234567890'],
            [
                'parent_user_id' => $parent->id,
                'school_class_id' => $class->id,
                'name' => 'Siswa Test A',
                'gender' => 'L',
                'entry_year' => '2025',
                'status' => 'active',
            ]
        );

        $student2 = Student::firstOrCreate(
            ['nisn' => '0987654321'],
            [
                'parent_user_id' => $parent->id,
                'school_class_id' => $class->id,
                'name' => 'Siswa Test B',
                'gender' => 'P',
                'entry_year' => '2025',
                'status' => 'active',
            ]
        );

        // 6. Create Sample Invoices
        // Invoice Lunas (Bulan Lalu)
        Invoice::create([
            'student_id' => $student1->id,
            'fee_type_id' => $sppFee->id,
            'amount' => 250000,
            'due_date' => now()->subMonth()->setDay(10),
            'period_month' => now()->subMonth()->month,
            'period_year' => now()->subMonth()->year,
            'status' => 'paid',
            'generated_by' => $admin->id,
        ]);

        // Invoice Belum Bayar (Bulan Ini) - Siswa A
        Invoice::create([
            'student_id' => $student1->id,
            'fee_type_id' => $sppFee->id,
            'amount' => 250000,
            'due_date' => now()->setDay(10),
            'period_month' => now()->month,
            'period_year' => now()->year,
            'status' => 'unpaid',
            'generated_by' => $admin->id,
        ]);

        // Invoice Belum Bayar (Bulan Ini) - Siswa B
        Invoice::create([
            'student_id' => $student2->id,
            'fee_type_id' => $sppFee->id,
            'amount' => 250000,
            'due_date' => now()->setDay(10),
            'period_month' => now()->month,
            'period_year' => now()->year,
            'status' => 'unpaid',
            'generated_by' => $admin->id,
        ]);
    }
}

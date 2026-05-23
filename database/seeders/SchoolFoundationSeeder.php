<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SchoolFoundationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure Roles exist
        $this->call(RoleSeeder::class);

        // 2. Create Staff Users (Admin & Guru) for Homeroom assignments
        $staffUsers = [];
        $names = ['Admin Akademik', 'Budi Guru', 'Siti Guru'];

        foreach ($names as $name) {
            $user = User::firstOrCreate(
                ['email' => strtolower(str_replace(' ', '.', $name)).'@schoolpay.test'],
                [
                    'name' => $name,
                    'password' => Hash::make('password'),
                ]
            );

            if (str_contains($name, 'Admin')) {
                $user->assignRole('Admin Akademik');
            } else {
                $user->assignRole('Admin Akademik'); // Default for homeroom
            }

            $staffUsers[] = $user;
        }

        // 3. Create 1 Academic Year
        $academicYear = AcademicYear::firstOrCreate(
            ['name' => '2025/2026'],
            [
                'start_date' => '2025-07-01',
                'end_date' => '2026-06-30',
                'is_active' => true,
            ]
        );

        // 4. Create Exactly 3 Classes (7A, 8A, 9A)
        $grades = ['7', '8', '9'];

        foreach ($grades as $index => $grade) {
            SchoolClass::firstOrCreate(
                [
                    'name' => "Kelas $grade A",
                    'academic_year_id' => $academicYear->id,
                ],
                [
                    'grade' => $grade,
                    'capacity' => 32,
                    'homeroom_id' => $staffUsers[$index % count($staffUsers)]->id,
                ]
            );
        }
    }
}

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

        // 3. Create 3 Academic Years
        $academicYearsData = [
            ['name' => '2024/2025', 'start_date' => '2024-07-01', 'end_date' => '2025-06-30', 'is_active' => false],
            ['name' => '2025/2026', 'start_date' => '2025-07-01', 'end_date' => '2026-06-30', 'is_active' => true],
            ['name' => '2026/2027', 'start_date' => '2026-07-01', 'end_date' => '2027-06-30', 'is_active' => false],
        ];

        $createdAcademicYears = [];
        foreach ($academicYearsData as $ay) {
            $createdAcademicYears[] = AcademicYear::firstOrCreate(
                ['name' => $ay['name']],
                [
                    'start_date' => $ay['start_date'],
                    'end_date' => $ay['end_date'],
                    'is_active' => $ay['is_active'],
                ]
            );
        }

        // 4. Create Classes for 6 Grade Levels (Kelas 1 SMP s/d Kelas 3 SMA) with 2 sections (A, B) for each Academic Year
        $grades = ['1', '2', '3', '4', '5', '6'];
        $sections = ['A', 'B'];

        foreach ($createdAcademicYears as $year) {
            foreach ($grades as $gradeIndex => $grade) {
                foreach ($sections as $sectionIndex => $section) {
                    SchoolClass::firstOrCreate(
                        [
                            'name' => "Kelas $grade$section",
                            'academic_year_id' => $year->id,
                        ],
                        [
                            'grade' => $grade,
                            'capacity' => 35, // Kapasitas 35 siswa per kelas
                            'homeroom_id' => $staffUsers[($gradeIndex + $sectionIndex) % count($staffUsers)]->id,
                        ]
                    );
                }
            }
        }
    }
}

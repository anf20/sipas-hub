<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = SchoolClass::with('academicYear')
            ->whereHas('academicYear', function ($query) {
                $query->where('is_active', true);
            })->get();

        if ($classes->isEmpty()) {
            $this->command->error('Pastikan SchoolFoundationSeeder sudah membuat kelas aktif.');

            return;
        }

        // Arrays of Islamic names
        $firstNamesMale = [
            'Muhammad', 'Ahmad', 'Ali', 'Utsman', 'Umar', 'Abu Bakar', 'Hasan', 'Husain', 'Hamzah',
            'Abdurrahman', 'Abdullah', 'Zaid', 'Khalid', 'Thariq', 'Anas', 'Bilal', 'Saad', 'Salman',
            'Luqman', 'Yusuf', 'Ibrahim', 'Ismail', 'Yahya', 'Zakaria', 'Yunus', 'Ayyub', 'Harun',
            'Sulaeman', 'Idris', 'Nuh', 'Luth', 'Hud', 'Shaleh', 'Adam', 'Fathir', 'Gibran', 'Rayan',
            'Zafran', 'Fathan', 'Zayan', 'Afiq', 'Arfan', 'Daffa', 'Faris', 'Nabil', 'Raffa', 'Hafizh',
            'Raziq', 'Fatih', 'Syamil',
        ];

        $firstNamesFemale = [
            'Aisyah', 'Fathimah', 'Khadijah', 'Zainab', 'Ruqayyah', 'Ummu Kultsum', 'Siti', 'Mariyam',
            'Asiyah', 'Hajar', 'Sarah', 'Kamilah', 'Safiyyah', 'Hafshah', 'Saudah', 'Juwayriyah',
            'Maimunah', 'Rayhanah', 'Shofia', 'Naura', 'Alya', 'Salma', 'Zahra', 'Laila', 'Nabila',
            'Yasmin', 'Farida', 'Wardah', 'Zhafira', 'Balqis', 'Alifa', 'Amira', 'Hana', 'Rania',
            'Talita', 'Faras', 'Keysha', 'Sabrina', 'Zaskia', 'Adiba', 'Azza',
        ];

        $lastNames = [
            'Al-Fatih', 'Al-Ghifari', 'Al-Farabi', 'Al-Khawarizmi', 'Al-Ghazali', 'Ar-Rasyid', 'Ad-Din',
            'Ramadhan', 'Maulana', 'Firdaus', 'Al-Bukhari', 'Al-Majid', 'Sholeh', 'Sholehah', 'Pratama',
            'Hidayat', 'Putra', 'Putri', 'Sari', 'Utami', 'Anwar', 'Zulkarnain', 'Nugroho', 'Wibowo',
        ];

        $generateIslamicName = function ($gender) use ($firstNamesMale, $firstNamesFemale, $lastNames) {
            $firstName = $gender === 'L'
                ? $firstNamesMale[array_rand($firstNamesMale)]
                : $firstNamesFemale[array_rand($firstNamesFemale)];
            $lastName = $lastNames[array_rand($lastNames)];

            return $firstName.' '.$lastName;
        };

        $this->command->info('Membuat 80 Wali Murid dengan Nama Islami...');

        // Create exactly 80 parents
        $parents = [];
        for ($i = 1; $i <= 80; $i++) {
            $parentGender = rand(0, 1) ? 'L' : 'P';
            $parentName = $generateIslamicName($parentGender);
            $phone = '628'.rand(111111111, 999999999);

            $parent = User::factory()->create([
                'name' => $parentName,
                'email' => "wali{$i}@test.com",
                'phone' => $phone,
                'password' => Hash::make('password'),
            ]);
            $parent->assignRole('Orang Tua');
            $parents[] = $parent;
        }

        // We have 80 parents.
        // 20 parents will have exactly 2 students (40 students).
        // 60 parents will have exactly 1 student (60 students).
        // Total students = 100.
        $studentAssignments = [];

        $shuffledParents = $parents;
        shuffle($shuffledParents);

        $doubleStudentParents = array_slice($shuffledParents, 0, 20);
        $singleStudentParents = array_slice($shuffledParents, 20, 60);

        // Build list of students with parent associations
        foreach ($doubleStudentParents as $parent) {
            $studentAssignments[] = ['parent' => $parent];
            $studentAssignments[] = ['parent' => $parent];
        }
        foreach ($singleStudentParents as $parent) {
            $studentAssignments[] = ['parent' => $parent];
        }

        $totalStudents = count($studentAssignments);
        $this->command->info("Membuat exactly {$totalStudents} Siswa (Santri)...");

        // Shuffle students so siblings are not necessarily created adjacently or in same classes
        shuffle($studentAssignments);

        // Assign classes evenly
        $classCount = $classes->count();
        foreach ($studentAssignments as $index => $assignment) {
            $assignedClass = $classes[$index % $classCount];
            $gender = rand(0, 1) ? 'L' : 'P';
            $studentName = $generateIslamicName($gender);

            Student::factory()->create([
                'name' => $studentName,
                'parent_user_id' => $assignment['parent']->id,
                'school_class_id' => $assignedClass->id,
                'current_grade' => $assignedClass->grade,
                'gender' => $gender,
                'entry_year' => $assignedClass->academicYear->start_date
                    ? date('Y', strtotime($assignedClass->academicYear->start_date))
                    : date('Y'),
            ]);
        }

        $this->command->info('Seeding data Santri & Wali Murid selesai dengan sukses!');
    }
}

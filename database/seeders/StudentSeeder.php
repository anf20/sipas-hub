<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Gunakan Faker dengan locale Indonesia
        $faker = Faker::create('id_ID');
        
        $classes = SchoolClass::with('academicYear')->get();

        if ($classes->isEmpty()) {
            $this->command->error('Pastikan SchoolFoundationSeeder sudah membuat kelas.');
            return;
        }

        $allStudentsData = [];
        
        // 1. Tentukan jumlah siswa (10-15) untuk setiap kelas
        foreach ($classes as $schoolClass) {
            $studentCount = rand(10, 15);
            for ($i = 0; $i < $studentCount; $i++) {
                $allStudentsData[] = [
                    'class' => $schoolClass,
                    // Buat nama unik Indonesia
                    'name' => $faker->unique()->name(),
                ];
            }
        }

        $totalStudents = count($allStudentsData);
        // 80% dari total siswa akan memiliki akun wali murid yang unik (1 anak = 1 ortu)
        // Sisa 20% anak akan menggunakan akun wali murid yang sudah ada (kakak-adik)
        $totalParents = (int) round($totalStudents * 0.8);
        
        $this->command->info("Membuat $totalStudents Siswa dan $totalParents Wali Murid...");

        // 2. Buat Akun Wali Murid
        $parents = [];
        for ($i = 1; $i <= $totalParents; $i++) {
            $parentName = $faker->unique()->name();
            $parent = User::factory()->create([
                'name' => $parentName,
                // Email format yang mudah diingat untuk demo (wali1@test.com, wali2@test.com, dsb)
                'email' => "wali{$i}@test.com", 
                'password' => Hash::make('password'),
            ]);
            $parent->assignRole('Orang Tua');
            $parents[] = $parent;
        }

        // 3. Buat Data Siswa dan kaitkan dengan Wali Murid
        foreach ($allStudentsData as $index => $studentData) {
            if ($index < $totalParents) {
                // 80% Siswa pertama memiliki orang tua sendiri yang unik
                $assignedParent = $parents[$index];
            } else {
                // 20% Siswa sisanya mengambil orang tua acak dari yang sudah dibuat (simulasi kakak-adik)
                $assignedParent = $parents[array_rand($parents)];
            }

            Student::factory()->create([
                'name' => $studentData['name'],
                'parent_user_id' => $assignedParent->id,
                'school_class_id' => $studentData['class']->id,
                'current_grade' => $studentData['class']->grade, // FIX: Mengisi jenjang kelas agar grafik tampil
                'entry_year' => $studentData['class']->academicYear->start_date
                    ? date('Y', strtotime($studentData['class']->academicYear->start_date))
                    : date('Y'),
            ]);
        }
    }
}

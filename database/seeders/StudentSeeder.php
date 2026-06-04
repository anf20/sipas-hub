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
        // 1. Ambil tepat 3 kelas yang sudah dibuat di SchoolFoundationSeeder
        $classes = SchoolClass::orderBy('grade')->take(3)->get();

        if ($classes->count() < 3) {
            $this->command->error('Pastikan SchoolFoundationSeeder sudah membuat 3 kelas.');

            return;
        }

        // 2. Buat antrian jatah kelas (10 siswa per kelas = 30 siswa)
        $classPool = [];
        foreach ($classes as $class) {
            for ($i = 0; $i < 10; $i++) {
                $classPool[] = $class;
            }
        }

        // Opsional: shuffle agar tidak berurutan, tapi tetap pasti 10 per kelas
        shuffle($classPool);

        // 3. Buat 20 Akun Wali Murid
        // Jatah: 10 wali x 2 siswa, 10 wali x 1 siswa
        $parentData = [
            ['count' => 10, 'students_per_parent' => 2],
            ['count' => 10, 'students_per_parent' => 1],
        ];

        $parentIndex = 1;
        foreach ($parentData as $group) {
            for ($i = 0; $group['count'] > $i; $i++) {
                // Buat Wali Murid
                $parent = User::factory()->create([
                    'name' => "Wali Murid $parentIndex",
                    'email' => "wali$parentIndex@test.com",
                    'password' => Hash::make('password'),
                ]);
                $parent->assignRole('Orang Tua');

                // Buat Siswa sesuai jatah
                for ($j = 0; $j < $group['students_per_parent']; $j++) {
                    $assignedClass = array_pop($classPool);

                    Student::factory()->create([
                        'parent_user_id' => $parent->id,
                        'school_class_id' => $assignedClass->id,
                        'entry_year' => $assignedClass->academicYear->start_date
                            ? date('Y', strtotime($assignedClass->academicYear->start_date))
                            : date('Y'),
                    ]);
                }
                $parentIndex++;
            }
        }
    }
}

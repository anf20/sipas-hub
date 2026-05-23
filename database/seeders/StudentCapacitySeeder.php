<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentCapacitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = SchoolClass::all();

        foreach ($classes as $class) {
            $currentStudentCount = $class->students()->count();
            $targetCount = (int) ($class->capacity * 0.7);
            $needed = $targetCount - $currentStudentCount;

            if ($needed > 0) {
                Student::factory()->count($needed)->create([
                    'school_class_id' => $class->id,
                    'entry_year' => $class->academicYear->start_date ? date('Y', strtotime($class->academicYear->start_date)) : date('Y'),
                ]);
            }
        }
    }
}

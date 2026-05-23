<?php

namespace Database\Factories;

use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nis' => fake()->unique()->numerify('##########'),
            'name' => fake()->name(),
            'school_class_id' => SchoolClass::factory(),
            'gender' => fake()->randomElement(['L', 'P']),
            'birth_date' => fake()->date(),
            'address' => fake()->address(),
            'status' => 'aktif',
            'entry_year' => date('Y'),
        ];
    }
}

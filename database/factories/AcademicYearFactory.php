<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AcademicYear>
 */
class AcademicYearFactory extends Factory
{
    protected $model = AcademicYear::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $year = fake()->unique()->numberBetween(2020, 2030);

        return [
            'name' => $year.'/'.($year + 1),
            'start_date' => $year.'-07-01',
            'end_date' => ($year + 1).'-06-30',
            'is_active' => false,
        ];
    }
}

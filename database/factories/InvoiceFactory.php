<?php

namespace Database\Factories;

use App\Models\FeeType;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'fee_type_id' => FeeType::factory(),
            'amount' => $this->faker->numberBetween(10000, 1000000),
            'due_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'period_month' => $this->faker->month(),
            'period_year' => $this->faker->year(),
            'status' => 'unpaid',
            'generated_by' => User::factory(),
        ];
    }
}

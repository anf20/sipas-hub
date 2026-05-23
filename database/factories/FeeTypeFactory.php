<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FeeType>
 */
class FeeTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'category' => 'SPP',
            'default_amount' => $this->faker->numberBetween(10000, 500000),
            'is_recurring' => true,
            'recurrence' => 'bulanan',
            'applicable_grades' => json_encode(['7', '8', '9']),
            'is_active' => true,
        ];
    }
}

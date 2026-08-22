<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RecallTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['6-Month Cleaning', '3-Month Perio Follow-up', 'Post-Extraction Follow-up', 'Root Canal Follow-up']),
            'default_interval_months' => fake()->randomElement([3, 6, 12]),
            'is_active' => true,
        ];
    }
}

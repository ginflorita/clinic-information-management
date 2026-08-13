<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ChairFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Chair '.fake()->unique()->numberBetween(1, 999),
            'location' => fake()->randomElement(['Room A', 'Room B', 'Room C']),
            'is_active' => true,
        ];
    }
}

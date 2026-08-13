<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ToothConditionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('COND-###')),
            'name' => fake()->words(2, true),
            'category' => fake()->randomElement(['restorative', 'surgical', 'periodontal']),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}

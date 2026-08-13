<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ToothFactory extends Factory
{
    public function definition(): array
    {
        return [
            'notation_system' => 'FDI',
            'tooth_code' => (string) fake()->unique()->numberBetween(11, 48),
            'tooth_name' => fake()->words(2, true),
            'dentition_type' => 'permanent',
            'arch' => fake()->randomElement(['maxillary', 'mandibular']),
            'position' => fake()->numberBetween(1, 32),
            'is_active' => true,
        ];
    }
}

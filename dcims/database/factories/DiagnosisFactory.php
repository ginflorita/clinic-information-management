<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DiagnosisFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('DX-###')),
            'name' => fake()->words(2, true),
            'category' => fake()->randomElement(['caries', 'periodontal', 'endodontic', 'other']),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}

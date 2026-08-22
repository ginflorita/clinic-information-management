<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ConsentTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Treatment Consent', 'Extraction Consent', 'Surgery Consent', 'Data Privacy Consent']),
            'description' => fake()->optional()->sentence(),
            'is_active' => true,
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProviderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'role' => fake()->randomElement(['dentist', 'hygienist', 'assistant']),
            'license_number' => strtoupper(fake()->unique()->bothify('LIC-#####')),
            'specialization' => fake()->randomElement(['General Dentistry', 'Orthodontics', 'Endodontics', null]),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'is_active' => true,
        ];
    }
}

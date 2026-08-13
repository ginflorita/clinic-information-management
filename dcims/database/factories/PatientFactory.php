<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->lastName(),
            'last_name' => fake()->lastName(),
            'suffix' => null,
            'preferred_name' => null,
            'date_of_birth' => fake()->dateTimeBetween('-80 years', '-2 years')->format('Y-m-d'),
            'sex' => fake()->randomElement(['male', 'female']),
            'civil_status' => fake()->randomElement(['single', 'married', 'widowed', 'separated']),
            'occupation' => fake()->jobTitle(),
            'email' => fake()->unique()->safeEmail(),
            'registration_date' => now()->format('Y-m-d'),
            'referral_source' => fake()->randomElement(['walk-in', 'referral', 'social media', 'insurance']),
            'status' => 'active',
        ];
    }
}

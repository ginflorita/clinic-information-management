<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LabFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company().' Dental Lab',
            'contact_person' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->companyEmail(),
            'address' => fake()->address(),
            'is_active' => true,
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'default_duration_minutes' => fake()->randomElement([15, 30, 45, 60]),
            'color' => fake()->safeHexColor(),
            'is_active' => true,
        ];
    }
}

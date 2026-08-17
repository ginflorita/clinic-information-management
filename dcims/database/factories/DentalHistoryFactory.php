<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class DentalHistoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'previous_dentist' => fake()->name(),
            'previous_treatments' => fake()->optional()->sentence(),
            'previous_extraction' => fake()->boolean(),
            'previous_root_canal' => fake()->boolean(),
            'prosthetic_history' => null,
            'orthodontic_history' => null,
            'previous_surgery' => null,
            'previous_complications' => null,
            'dental_habits' => fake()->randomElement(['bruxism', 'nail biting', null]),
            'oral_hygiene' => fake()->randomElement(['brushes twice daily', 'brushes once daily']),
            'chief_concerns' => fake()->sentence(),
            'recorded_at' => now(),
        ];
    }
}

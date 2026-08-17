<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientConditionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'condition_name' => fake()->randomElement(['Diabetes', 'Hypertension', 'Asthma', 'Heart Disease']),
            'status' => 'active',
            'diagnosed_date' => fake()->dateTimeBetween('-10 years', 'now')->format('Y-m-d'),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}

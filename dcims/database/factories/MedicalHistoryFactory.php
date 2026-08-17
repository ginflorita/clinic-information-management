<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class MedicalHistoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'previous_surgeries' => fake()->optional()->sentence(),
            'hospitalization' => fake()->optional()->sentence(),
            'current_medications' => fake()->optional()->sentence(),
            'pregnancy_status' => null,
            'smoking_status' => fake()->randomElement(['non-smoker', 'former smoker', 'current smoker']),
            'alcohol_use' => fake()->randomElement(['none', 'occasional', 'regular']),
            'family_medical_history' => fake()->optional()->sentence(),
            'physician_name' => fake()->name(),
            'physician_contact' => fake()->phoneNumber(),
            'medical_alerts' => null,
            'recorded_at' => now(),
        ];
    }
}

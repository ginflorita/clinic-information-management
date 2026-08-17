<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;

class EncounterFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'appointment_id' => null,
            'provider_id' => Provider::factory(),
            'encounter_type' => 'visit',
            'status' => 'in_progress',
            'started_at' => now(),
            'chief_complaint' => fake()->sentence(),
        ];
    }
}

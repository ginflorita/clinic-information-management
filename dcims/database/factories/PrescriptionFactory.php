<?php

namespace Database\Factories;

use App\Models\Encounter;
use App\Models\Patient;
use App\Models\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;

class PrescriptionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'encounter_id' => Encounter::factory(),
            'provider_id' => Provider::factory(),
            'status' => 'active',
            'prescribed_at' => now(),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;

class TreatmentPlanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'provider_id' => Provider::factory(),
            'title' => fake()->words(3, true),
            'status' => 'draft',
            'notes' => null,
        ];
    }
}

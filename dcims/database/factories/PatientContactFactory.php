<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientContactFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'contact_type' => 'mobile',
            'contact_value' => fake()->phoneNumber(),
            'is_primary' => true,
            'verified_at' => null,
        ];
    }
}

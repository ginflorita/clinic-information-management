<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientIdentifierFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'identifier_type' => 'philhealth',
            'identifier_value' => fake()->numerify('##-#########-#'),
            'issuing_authority' => 'PhilHealth',
            'issued_at' => fake()->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
            'expires_at' => null,
        ];
    }
}

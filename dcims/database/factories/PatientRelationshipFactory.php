<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientRelationshipFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'related_patient_id' => null,
            'contact_name' => fake()->name(),
            'contact_phone' => fake()->phoneNumber(),
            'relationship_type' => fake()->randomElement(['mother', 'father', 'spouse', 'sibling', 'guardian']),
            'is_guardian' => false,
            'is_emergency_contact' => true,
        ];
    }

    public function forRelatedPatient(): static
    {
        return $this->state(fn () => [
            'related_patient_id' => Patient::factory(),
            'contact_name' => null,
            'contact_phone' => null,
        ]);
    }
}

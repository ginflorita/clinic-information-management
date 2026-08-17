<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientAllergyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'allergen' => fake()->randomElement(['Penicillin', 'Amoxicillin', 'Latex', 'Local Anesthetic', 'NSAIDs']),
            'reaction' => fake()->randomElement(['Rash', 'Swelling', 'Anaphylaxis', 'Hives']),
            'severity' => fake()->randomElement(['mild', 'moderate', 'severe']),
            'onset_date' => fake()->optional()->dateTimeBetween('-10 years', 'now')?->format('Y-m-d'),
            'notes' => fake()->optional()->sentence(),
            'status' => 'active',
        ];
    }
}

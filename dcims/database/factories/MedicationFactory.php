<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MedicationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'generic_name' => fake()->randomElement(['Amoxicillin', 'Ibuprofen', 'Paracetamol', 'Chlorhexidine']),
            'brand_name' => fake()->optional()->word(),
            'dosage_form' => fake()->randomElement(['tablet', 'capsule', 'syrup', 'mouthwash']),
            'strength' => fake()->randomElement(['250', '500', '2%']),
            'unit' => fake()->randomElement(['mg', 'ml', '%']),
            'is_active' => true,
        ];
    }
}

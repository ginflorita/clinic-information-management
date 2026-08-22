<?php

namespace Database\Factories;

use App\Models\Medication;
use App\Models\Prescription;
use Illuminate\Database\Eloquent\Factories\Factory;

class PrescriptionItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'prescription_id' => Prescription::factory(),
            'medication_id' => Medication::factory(),
            'dose' => fake()->randomElement(['1 tablet', '500mg', '2 capsules']),
            'frequency' => fake()->randomElement(['once daily', 'twice daily', 'every 8 hours']),
            'route' => 'oral',
            'duration' => fake()->randomElement(['5 days', '7 days', '10 days']),
            'quantity' => fake()->numberBetween(5, 30),
            'instructions' => fake()->optional()->sentence(),
            'refills' => 0,
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class QueueEntryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'appointment_id' => null,
            'queue_date' => now()->format('Y-m-d'),
            'queue_number' => fake()->unique()->numberBetween(1, 1000),
            'status' => 'waiting',
            'checked_in_at' => now(),
        ];
    }
}

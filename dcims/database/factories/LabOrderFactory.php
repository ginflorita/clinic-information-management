<?php

namespace Database\Factories;

use App\Models\Lab;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LabOrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'lab_id' => Lab::factory(),
            'expected_date' => now()->addWeek()->toDateString(),
            'status' => 'pending',
            'cost' => fake()->randomFloat(2, 500, 5000),
            'notes' => fake()->optional()->sentence(),
            'created_by' => User::factory(),
        ];
    }
}

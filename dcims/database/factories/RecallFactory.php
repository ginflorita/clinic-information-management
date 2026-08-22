<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\RecallType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecallFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'recall_type_id' => RecallType::factory(),
            'due_date' => now()->addMonths(6)->toDateString(),
            'status' => 'pending',
            'notes' => fake()->optional()->sentence(),
            'created_by' => User::factory(),
        ];
    }
}

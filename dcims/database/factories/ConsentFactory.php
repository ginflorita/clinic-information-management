<?php

namespace Database\Factories;

use App\Models\ConsentType;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConsentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'consent_type_id' => ConsentType::factory(),
            'status' => 'granted',
            'granted_at' => now(),
            'obtained_by' => User::factory(),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}

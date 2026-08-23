<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReferralFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'referring_provider_id' => Provider::factory(),
            'receiving_name' => fake()->name(),
            'receiving_specialty' => fake()->randomElement(['Oral Surgery', 'Orthodontics', 'Endodontics', 'Periodontics']),
            'receiving_contact' => fake()->phoneNumber(),
            'reason' => fake()->sentence(),
            'clinical_summary' => fake()->optional()->paragraph(),
            'referral_date' => now()->toDateString(),
            'status' => 'draft',
            'created_by' => User::factory(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\AppointmentType;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'appointment_type_id' => AppointmentType::factory(),
            'preferred_date' => now()->addDays(7)->toDateString(),
            'preferred_time_period' => fake()->randomElement(['morning', 'afternoon', 'evening']),
            'reason' => fake()->sentence(),
            'contact_phone' => fake()->phoneNumber(),
            'contact_email' => fake()->safeEmail(),
            'status' => 'pending',
        ];
    }
}

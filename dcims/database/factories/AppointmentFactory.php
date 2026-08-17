<?php

namespace Database\Factories;

use App\Models\AppointmentType;
use App\Models\Chair;
use App\Models\Patient;
use App\Models\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('+1 day', '+30 days')->setTime(fake()->numberBetween(8, 16), fake()->randomElement([0, 30]));
        $end = (clone $start)->modify('+30 minutes');

        return [
            'patient_id' => Patient::factory(),
            'provider_id' => Provider::factory(),
            'appointment_type_id' => AppointmentType::factory(),
            'chair_id' => Chair::factory(),
            'scheduled_start' => $start,
            'scheduled_end' => $end,
            'status' => 'scheduled',
            'reason' => fake()->sentence(4),
        ];
    }
}

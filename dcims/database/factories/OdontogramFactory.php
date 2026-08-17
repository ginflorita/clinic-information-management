<?php

namespace Database\Factories;

use App\Models\Encounter;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OdontogramFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'encounter_id' => Encounter::factory(),
            'dentition_type' => 'permanent',
            'notation_system' => 'FDI',
            'recorded_at' => now(),
            'recorded_by' => User::factory(),
        ];
    }
}

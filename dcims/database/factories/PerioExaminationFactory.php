<?php

namespace Database\Factories;

use App\Models\Encounter;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PerioExaminationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'encounter_id' => Encounter::factory(),
            'examined_at' => now(),
            'examined_by' => User::factory(),
        ];
    }
}

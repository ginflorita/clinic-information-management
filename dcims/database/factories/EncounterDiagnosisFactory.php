<?php

namespace Database\Factories;

use App\Models\Diagnosis;
use App\Models\Encounter;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EncounterDiagnosisFactory extends Factory
{
    public function definition(): array
    {
        return [
            'encounter_id' => Encounter::factory(),
            'patient_id' => Patient::factory(),
            'diagnosis_id' => Diagnosis::factory(),
            'tooth_id' => null,
            'status' => 'active',
            'notes' => null,
            'diagnosed_by' => User::factory(),
            'diagnosed_at' => now(),
        ];
    }
}

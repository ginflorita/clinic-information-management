<?php

namespace Database\Factories;

use App\Models\Encounter;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClinicalNoteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'encounter_id' => Encounter::factory(),
            'note_type' => 'progress',
            'note_text' => fake()->paragraph(),
            'status' => 'draft',
            'created_by' => User::factory(),
        ];
    }
}

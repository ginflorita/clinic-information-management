<?php

namespace Database\Factories;

use App\Models\Odontogram;
use App\Models\Tooth;
use App\Models\ToothCondition;
use Illuminate\Database\Eloquent\Factories\Factory;

class OdontogramEntryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'odontogram_id' => Odontogram::factory(),
            'tooth_id' => Tooth::factory(),
            'condition_id' => ToothCondition::factory(),
            'status' => 'active',
            'notes' => null,
        ];
    }
}

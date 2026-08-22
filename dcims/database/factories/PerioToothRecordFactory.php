<?php

namespace Database\Factories;

use App\Models\PerioExamination;
use App\Models\Tooth;
use Illuminate\Database\Eloquent\Factories\Factory;

class PerioToothRecordFactory extends Factory
{
    public function definition(): array
    {
        return [
            'perio_examination_id' => PerioExamination::factory(),
            'tooth_id' => Tooth::factory(),
            'mobility' => null,
            'furcation' => null,
            'notes' => null,
        ];
    }
}

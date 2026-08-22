<?php

namespace Database\Factories;

use App\Models\PerioSiteMeasurement;
use App\Models\PerioToothRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

class PerioSiteMeasurementFactory extends Factory
{
    public function definition(): array
    {
        return [
            'perio_tooth_record_id' => PerioToothRecord::factory(),
            'site' => fake()->randomElement(PerioSiteMeasurement::SITES),
            'probing_depth' => fake()->randomFloat(1, 1, 6),
            'bleeding_on_probing' => false,
            'plaque_present' => false,
        ];
    }
}

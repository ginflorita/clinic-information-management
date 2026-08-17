<?php

namespace Database\Factories;

use App\Models\OdontogramEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

class OdontogramEntrySurfaceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'odontogram_entry_id' => OdontogramEntry::factory(),
            'surface' => fake()->randomElement(['M', 'D', 'O', 'I', 'B', 'L', 'P', 'F']),
        ];
    }
}

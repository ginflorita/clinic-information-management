<?php

namespace Database\Factories;

use App\Models\ProcedureCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProcedureFactory extends Factory
{
    public function definition(): array
    {
        return [
            'procedure_category_id' => ProcedureCategory::factory(),
            'code' => strtoupper(fake()->unique()->bothify('PRC-###')),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'default_fee' => fake()->randomFloat(2, 200, 5000),
            'default_duration_minutes' => fake()->randomElement([15, 30, 45, 60, 90]),
            'is_active' => true,
        ];
    }
}

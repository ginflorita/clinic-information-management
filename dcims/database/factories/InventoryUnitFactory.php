<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryUnitFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(['Box', 'Piece', 'Pack', 'Bottle', 'Roll']),
            'abbreviation' => fake()->lexify('??'),
            'is_active' => true,
        ];
    }
}

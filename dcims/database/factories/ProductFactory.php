<?php

namespace Database\Factories;

use App\Models\InventoryCategory;
use App\Models\InventoryUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sku' => strtoupper(fake()->unique()->bothify('SKU-????-###')),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'category_id' => InventoryCategory::factory(),
            'unit_id' => InventoryUnit::factory(),
            'reorder_level' => 10,
            'is_active' => true,
        ];
    }
}

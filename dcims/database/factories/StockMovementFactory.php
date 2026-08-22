<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductBatch;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockMovementFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'batch_id' => ProductBatch::factory(),
            'movement_type' => 'stock_in',
            'quantity' => fake()->randomFloat(2, 1, 50),
            'movement_date' => now()->toDateString(),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}

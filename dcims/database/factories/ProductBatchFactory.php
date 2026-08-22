<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductBatchFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'supplier_id' => Supplier::factory(),
            'batch_number' => strtoupper(fake()->bothify('BATCH-####')),
            'lot_number' => fake()->bothify('LOT-####'),
            'expiry_date' => fake()->dateTimeBetween('+1 month', '+2 years')->format('Y-m-d'),
            'quantity' => fake()->randomFloat(2, 10, 100),
            'unit_cost' => fake()->randomFloat(2, 5, 500),
            'received_at' => now()->toDateString(),
        ];
    }
}

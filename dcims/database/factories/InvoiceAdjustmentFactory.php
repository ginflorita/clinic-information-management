<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceAdjustmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'invoice_id' => Invoice::factory(),
            'type' => 'discount',
            'amount' => fake()->randomFloat(2, 50, 1000),
            'reason' => fake()->sentence(),
            'created_by' => User::factory(),
        ];
    }
}

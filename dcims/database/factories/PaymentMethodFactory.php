<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentMethodFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(['Cash', 'Credit Card', 'Debit Card', 'Bank Transfer', 'Insurance', 'GCash']),
            'is_active' => true,
        ];
    }
}

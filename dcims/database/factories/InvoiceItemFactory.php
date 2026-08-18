<?php

namespace Database\Factories;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceItemFactory extends Factory
{
    public function definition(): array
    {
        $unitPrice = fake()->randomFloat(2, 500, 15000);
        $quantity = 1;

        return [
            'invoice_id' => Invoice::factory(),
            'procedure_id' => null,
            'treatment_plan_item_id' => null,
            'procedure_record_id' => null,
            'description' => fake()->words(3, true),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'discount_amount' => 0,
            'total_amount' => $unitPrice * $quantity,
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        $total = fake()->randomFloat(2, 1000, 20000);

        return [
            'patient_id' => Patient::factory(),
            'encounter_id' => null,
            'invoice_date' => now()->toDateString(),
            'due_date' => null,
            'status' => 'issued',
            'subtotal' => $total,
            'discount_amount' => 0,
            'tax_amount' => 0,
            'total_amount' => $total,
        ];
    }
}

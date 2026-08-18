<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'invoice_id' => null,
            'payment_date' => now()->toDateString(),
            'payment_method_id' => PaymentMethod::factory(),
            'amount' => fake()->randomFloat(2, 100, 10000),
            'reference_number' => null,
            'status' => 'completed',
            'received_by' => User::factory(),
            'notes' => null,
        ];
    }
}

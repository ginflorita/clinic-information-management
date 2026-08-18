<?php

namespace Database\Factories;

use App\Models\Encounter;
use App\Models\Patient;
use App\Models\Procedure;
use App\Models\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProcedureRecordFactory extends Factory
{
    public function definition(): array
    {
        $unitPrice = fake()->randomFloat(2, 500, 15000);
        $quantity = 1;

        return [
            'encounter_id' => Encounter::factory(),
            'procedure_id' => Procedure::factory(),
            'patient_id' => Patient::factory(),
            'provider_id' => Provider::factory(),
            'tooth_id' => null,
            'treatment_plan_item_id' => null,
            'status' => 'completed',
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_amount' => $unitPrice * $quantity,
            'performed_at' => now(),
            'notes' => null,
        ];
    }
}

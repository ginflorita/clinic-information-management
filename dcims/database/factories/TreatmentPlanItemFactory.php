<?php

namespace Database\Factories;

use App\Models\Procedure;
use App\Models\TreatmentPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

class TreatmentPlanItemFactory extends Factory
{
    public function definition(): array
    {
        $unitPrice = fake()->randomFloat(2, 500, 15000);
        $quantity = 1;

        return [
            'treatment_plan_id' => TreatmentPlan::factory(),
            'procedure_id' => Procedure::factory(),
            'tooth_id' => null,
            'status' => 'proposed',
            'quantity' => $quantity,
            'estimated_unit_price' => $unitPrice,
            'estimated_total' => $unitPrice * $quantity,
            'priority' => null,
            'notes' => null,
        ];
    }
}

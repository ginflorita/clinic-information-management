<?php

namespace App\Http\Controllers;

use App\Models\Procedure;
use App\Models\TreatmentPlan;
use App\Models\TreatmentPlanItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TreatmentPlanItemController extends Controller
{
    public function store(Request $request, TreatmentPlan $treatmentPlan): RedirectResponse
    {
        $data = $request->validate([
            'procedure_id' => ['required', 'exists:procedures,id'],
            'tooth_id' => ['nullable', 'exists:teeth,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'estimated_unit_price' => ['nullable', 'numeric', 'min:0'],
            'priority' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ]);

        $unitPrice = $data['estimated_unit_price'] ?? Procedure::findOrFail($data['procedure_id'])->default_fee;

        $treatmentPlan->items()->create([
            'procedure_id' => $data['procedure_id'],
            'tooth_id' => $data['tooth_id'] ?? null,
            'quantity' => $data['quantity'],
            'estimated_unit_price' => $unitPrice,
            'estimated_total' => $unitPrice * $data['quantity'],
            'priority' => $data['priority'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->route('treatment-plans.show', $treatmentPlan)->with('status', 'Item added.');
    }

    public function updateStatus(Request $request, TreatmentPlan $treatmentPlan, TreatmentPlanItem $item): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(TreatmentPlanItem::STATUSES)],
        ]);

        $item->update(['status' => $data['status']]);

        return redirect()->route('treatment-plans.show', $treatmentPlan)->with('status', 'Item status updated.');
    }
}

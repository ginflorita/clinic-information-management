<?php

namespace App\Http\Controllers;

use App\Models\Encounter;
use App\Models\Procedure;
use App\Models\ProcedureRecord;
use App\Models\TreatmentPlanItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use LogicException;

class ProcedureRecordController extends Controller
{
    public function store(Request $request, Encounter $encounter): RedirectResponse
    {
        $data = $request->validate([
            'procedure_id' => ['required', 'exists:procedures,id'],
            'tooth_id' => ['nullable', 'exists:teeth,id'],
            'treatment_plan_item_id' => ['nullable', 'exists:treatment_plan_items,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $planItem = null;
        if (! empty($data['treatment_plan_item_id'])) {
            $planItem = TreatmentPlanItem::with('treatmentPlan')->findOrFail($data['treatment_plan_item_id']);

            if ($planItem->treatmentPlan->patient_id !== $encounter->patient_id) {
                throw ValidationException::withMessages([
                    'treatment_plan_item_id' => 'That treatment plan item does not belong to this patient.',
                ]);
            }
        }

        $unitPrice = $data['unit_price'] ?? Procedure::findOrFail($data['procedure_id'])->default_fee;

        $encounter->procedureRecords()->create([
            'procedure_id' => $data['procedure_id'],
            'patient_id' => $encounter->patient_id,
            'provider_id' => $encounter->provider_id,
            'tooth_id' => $data['tooth_id'] ?? null,
            'treatment_plan_item_id' => $planItem?->id,
            'quantity' => $data['quantity'],
            'unit_price' => $unitPrice,
            'total_amount' => $unitPrice * $data['quantity'],
            'performed_at' => now(),
            'notes' => $data['notes'] ?? null,
        ]);

        if ($planItem) {
            $planItem->update(['status' => 'completed']);
        }

        return redirect()->route('encounters.show', $encounter)->with('status', 'Procedure recorded.');
    }

    public function void(Encounter $encounter, ProcedureRecord $procedureRecord): RedirectResponse
    {
        try {
            $procedureRecord->void();
        } catch (LogicException $e) {
            throw ValidationException::withMessages(['status' => $e->getMessage()]);
        }

        return redirect()->route('encounters.show', $encounter)->with('status', 'Procedure record voided.');
    }
}

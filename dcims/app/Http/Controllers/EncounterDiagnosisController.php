<?php

namespace App\Http\Controllers;

use App\Models\Encounter;
use App\Models\EncounterDiagnosis;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EncounterDiagnosisController extends Controller
{
    public function store(Request $request, Encounter $encounter): RedirectResponse
    {
        $data = $request->validate([
            'diagnosis_id' => ['required', 'exists:diagnoses,id'],
            'tooth_id' => ['nullable', 'exists:teeth,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $encounter->diagnoses()->create([
            'patient_id' => $encounter->patient_id,
            'diagnosis_id' => $data['diagnosis_id'],
            'tooth_id' => $data['tooth_id'] ?? null,
            'notes' => $data['notes'] ?? null,
            'diagnosed_by' => $request->user()->id,
            'diagnosed_at' => now(),
        ]);

        return redirect()->route('encounters.show', $encounter)->with('status', 'Diagnosis recorded.');
    }

    public function updateStatus(Request $request, Encounter $encounter, EncounterDiagnosis $encounterDiagnosis): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(EncounterDiagnosis::STATUSES)],
        ]);

        $encounterDiagnosis->update(['status' => $data['status']]);

        return redirect()->route('encounters.show', $encounter)->with('status', 'Diagnosis status updated.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Encounter;
use App\Models\Odontogram;
use App\Models\OdontogramEntrySurface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OdontogramEntryController extends Controller
{
    public function store(Request $request, Encounter $encounter): RedirectResponse
    {
        $data = $request->validate([
            'tooth_id' => ['required', 'exists:teeth,id'],
            'condition_id' => ['required', 'exists:tooth_conditions,id'],
            'notes' => ['nullable', 'string'],
            'surfaces' => ['nullable', 'array'],
            'surfaces.*' => ['string', 'in:'.implode(',', OdontogramEntrySurface::SURFACES)],
        ]);

        $odontogram = $encounter->odontogram ?? Odontogram::create([
            'patient_id' => $encounter->patient_id,
            'encounter_id' => $encounter->id,
            'recorded_at' => now(),
            'recorded_by' => $request->user()->id,
        ]);

        $entry = $odontogram->entries()->create([
            'tooth_id' => $data['tooth_id'],
            'condition_id' => $data['condition_id'],
            'notes' => $data['notes'] ?? null,
        ]);

        foreach ($data['surfaces'] ?? [] as $surface) {
            $entry->surfaces()->create(['surface' => $surface]);
        }

        return redirect()->route('encounters.show', $encounter)->with('status', 'Chart entry recorded.');
    }
}

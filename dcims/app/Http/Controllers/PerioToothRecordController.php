<?php

namespace App\Http\Controllers;

use App\Models\Encounter;
use App\Models\PerioExamination;
use App\Models\PerioSiteMeasurement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PerioToothRecordController extends Controller
{
    public function store(Request $request, Encounter $encounter): RedirectResponse
    {
        $data = $request->validate([
            'tooth_id' => ['required', 'exists:teeth,id'],
            'mobility' => ['nullable', 'integer', 'min:0', 'max:3'],
            'furcation' => ['nullable', 'integer', 'min:0', 'max:3'],
            'notes' => ['nullable', 'string'],
            'sites' => ['required', 'array'],
            'sites.*.probing_depth' => ['nullable', 'numeric', 'min:0', 'max:20'],
            'sites.*.gingival_recession' => ['nullable', 'numeric', 'min:-20', 'max:20'],
            'sites.*.clinical_attachment_level' => ['nullable', 'numeric', 'min:0', 'max:20'],
            'sites.*.gingival_margin' => ['nullable', 'numeric', 'min:-20', 'max:20'],
            'sites.*.bleeding_on_probing' => ['nullable', 'boolean'],
            'sites.*.plaque_present' => ['nullable', 'boolean'],
        ]);

        if (! empty(array_diff(array_keys($data['sites']), PerioSiteMeasurement::SITES))) {
            throw ValidationException::withMessages(['sites' => 'Invalid site.']);
        }

        $filledSites = collect($data['sites'])->filter(fn ($site) => isset($site['probing_depth']) && $site['probing_depth'] !== '');

        if ($filledSites->isEmpty()) {
            throw ValidationException::withMessages(['sites' => 'Enter at least one site measurement.']);
        }

        $examination = $encounter->perioExamination ?? PerioExamination::create([
            'patient_id' => $encounter->patient_id,
            'encounter_id' => $encounter->id,
            'examined_at' => now(),
            'examined_by' => $request->user()->id,
        ]);

        $toothRecord = $examination->toothRecords()->updateOrCreate(
            ['tooth_id' => $data['tooth_id']],
            [
                'mobility' => $data['mobility'] ?? null,
                'furcation' => $data['furcation'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]
        );

        foreach ($filledSites as $site => $measurement) {
            $toothRecord->measurements()->updateOrCreate(
                ['site' => $site],
                [
                    'probing_depth' => $measurement['probing_depth'],
                    'gingival_recession' => $measurement['gingival_recession'] ?? null,
                    'clinical_attachment_level' => $measurement['clinical_attachment_level'] ?? null,
                    'gingival_margin' => $measurement['gingival_margin'] ?? null,
                    'bleeding_on_probing' => $measurement['bleeding_on_probing'] ?? false,
                    'plaque_present' => $measurement['plaque_present'] ?? false,
                ]
            );
        }

        return redirect()->route('encounters.show', $encounter)->with('status', 'Periodontal measurements recorded.');
    }
}

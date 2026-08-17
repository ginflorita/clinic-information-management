<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientAllergy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PatientAllergyController extends Controller
{
    public function store(Request $request, Patient $patient): RedirectResponse
    {
        $data = $request->validate([
            'allergen' => ['required', 'string', 'max:255'],
            'reaction' => ['nullable', 'string', 'max:255'],
            'severity' => ['required', 'in:mild,moderate,severe'],
            'onset_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);
        $data['status'] = 'active';

        $patient->allergies()->create($data);

        return redirect()->route('patients.show', $patient)->with('status', 'Allergy added.');
    }

    public function destroy(Patient $patient, PatientAllergy $allergy): RedirectResponse
    {
        $allergy->delete();

        return redirect()->route('patients.show', $patient)->with('status', 'Allergy removed.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientRelationship;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PatientRelationshipController extends Controller
{
    public function store(Request $request, Patient $patient): RedirectResponse
    {
        $data = $request->validate([
            'related_patient_id' => ['nullable', 'exists:patients,id'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'relationship_type' => ['required', 'string', 'max:100'],
        ]);
        $data['is_guardian'] = $request->boolean('is_guardian');
        $data['is_emergency_contact'] = $request->boolean('is_emergency_contact');

        $hasRelated = filled($data['related_patient_id'] ?? null);
        $hasContactName = filled($data['contact_name'] ?? null);

        if ($hasRelated === $hasContactName) {
            throw ValidationException::withMessages([
                'contact_name' => 'Either link an existing patient or enter a name for a non-patient contact — not both, not neither.',
            ]);
        }

        $patient->relationships()->create($data);

        return redirect()->route('patients.show', $patient)->with('status', 'Relationship added.');
    }

    public function destroy(Patient $patient, PatientRelationship $relationship): RedirectResponse
    {
        $relationship->delete();

        return redirect()->route('patients.show', $patient)->with('status', 'Relationship removed.');
    }
}

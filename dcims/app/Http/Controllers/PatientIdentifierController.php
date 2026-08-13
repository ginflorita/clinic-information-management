<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientIdentifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PatientIdentifierController extends Controller
{
    public function store(Request $request, Patient $patient): RedirectResponse
    {
        $data = $request->validate([
            'identifier_type' => ['required', 'string', 'max:100'],
            'identifier_value' => ['required', 'string', 'max:255'],
            'issuing_authority' => ['nullable', 'string', 'max:255'],
            'issued_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:issued_at'],
        ]);

        $patient->identifiers()->create($data);

        return redirect()->route('patients.show', $patient)->with('status', 'Identifier added.');
    }

    public function destroy(Patient $patient, PatientIdentifier $identifier): RedirectResponse
    {
        $identifier->delete();

        return redirect()->route('patients.show', $patient)->with('status', 'Identifier removed.');
    }
}

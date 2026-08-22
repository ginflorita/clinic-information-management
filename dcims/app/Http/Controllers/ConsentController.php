<?php

namespace App\Http\Controllers;

use App\Models\Consent;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use LogicException;

class ConsentController extends Controller
{
    public function store(Request $request, Patient $patient): RedirectResponse
    {
        $data = $request->validate([
            'consent_type_id' => ['required', 'exists:consent_types,id'],
            'notes' => ['nullable', 'string'],
        ]);
        $data['granted_at'] = now();
        $data['obtained_by'] = $request->user()->id;

        $patient->consents()->create($data);

        return redirect()->route('patients.show', $patient)->with('status', 'Consent recorded.');
    }

    public function revoke(Patient $patient, Consent $consent): RedirectResponse
    {
        try {
            $consent->revoke();
        } catch (LogicException $e) {
            throw ValidationException::withMessages(['status' => $e->getMessage()]);
        }

        return redirect()->route('patients.show', $patient)->with('status', 'Consent revoked.');
    }
}

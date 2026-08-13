<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientContact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PatientContactController extends Controller
{
    public function store(Request $request, Patient $patient): RedirectResponse
    {
        $data = $request->validate([
            'contact_type' => ['required', 'in:mobile,telephone,email'],
            'contact_value' => ['required', 'string', 'max:255'],
        ]);
        $data['is_primary'] = $request->boolean('is_primary');

        $patient->contacts()->create($data);

        return redirect()->route('patients.show', $patient)->with('status', 'Contact added.');
    }

    public function destroy(Patient $patient, PatientContact $contact): RedirectResponse
    {
        $contact->delete();

        return redirect()->route('patients.show', $patient)->with('status', 'Contact removed.');
    }
}

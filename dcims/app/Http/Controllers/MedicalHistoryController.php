<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MedicalHistoryController extends Controller
{
    public function edit(Patient $patient): View
    {
        return view('patients.medical-history.edit', [
            'patient' => $patient,
            'medicalHistory' => $patient->medicalHistory,
        ]);
    }

    public function update(Request $request, Patient $patient): RedirectResponse
    {
        $data = $request->validate([
            'previous_surgeries' => ['nullable', 'string'],
            'hospitalization' => ['nullable', 'string'],
            'current_medications' => ['nullable', 'string'],
            'pregnancy_status' => ['nullable', 'string', 'max:255'],
            'smoking_status' => ['nullable', 'string', 'max:255'],
            'alcohol_use' => ['nullable', 'string', 'max:255'],
            'family_medical_history' => ['nullable', 'string'],
            'physician_name' => ['nullable', 'string', 'max:255'],
            'physician_contact' => ['nullable', 'string', 'max:255'],
            'medical_alerts' => ['nullable', 'string'],
        ]);
        $data['recorded_at'] = now();
        $data['recorded_by_user_id'] = $request->user()->id;

        $patient->medicalHistory()->updateOrCreate(['patient_id' => $patient->id], $data);

        return redirect()->route('patients.show', $patient)->with('status', 'Medical history saved.');
    }
}

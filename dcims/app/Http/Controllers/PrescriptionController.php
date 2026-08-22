<?php

namespace App\Http\Controllers;

use App\Models\Encounter;
use App\Models\Patient;
use App\Models\Prescription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use LogicException;

class PrescriptionController extends Controller
{
    public function store(Request $request, Encounter $encounter): RedirectResponse
    {
        $data = $request->validate([
            'notes' => ['nullable', 'string'],
        ]);

        $encounter->prescriptions()->create([
            'patient_id' => $encounter->patient_id,
            'provider_id' => $encounter->provider_id,
            'status' => 'active',
            'prescribed_at' => now(),
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->route('encounters.show', $encounter)->with('status', 'Prescription created.');
    }

    public function cancel(Encounter $encounter, Prescription $prescription): RedirectResponse
    {
        try {
            $prescription->cancel();
        } catch (LogicException $e) {
            throw ValidationException::withMessages(['status' => $e->getMessage()]);
        }

        return redirect()->route('encounters.show', $encounter)->with('status', 'Prescription cancelled.');
    }

    public function patientHistory(Patient $patient): View
    {
        $prescriptions = $patient->prescriptions()
            ->with(['items.medication', 'provider', 'encounter'])
            ->orderByDesc('prescribed_at')
            ->get();

        return view('patients.prescriptions', ['patient' => $patient, 'prescriptions' => $prescriptions]);
    }
}

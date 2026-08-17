<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientCondition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PatientConditionController extends Controller
{
    public function store(Request $request, Patient $patient): RedirectResponse
    {
        $data = $request->validate([
            'condition_name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:active,resolved,managed'],
            'diagnosed_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $patient->conditions()->create($data);

        return redirect()->route('patients.show', $patient)->with('status', 'Condition added.');
    }

    public function destroy(Patient $patient, PatientCondition $condition): RedirectResponse
    {
        $condition->delete();

        return redirect()->route('patients.show', $patient)->with('status', 'Condition removed.');
    }
}

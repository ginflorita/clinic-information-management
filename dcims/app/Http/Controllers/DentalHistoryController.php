<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DentalHistoryController extends Controller
{
    public function edit(Patient $patient): View
    {
        return view('patients.dental-history.edit', [
            'patient' => $patient,
            'dentalHistory' => $patient->dentalHistory,
        ]);
    }

    public function update(Request $request, Patient $patient): RedirectResponse
    {
        $data = $request->validate([
            'previous_dentist' => ['nullable', 'string', 'max:255'],
            'previous_treatments' => ['nullable', 'string'],
            'prosthetic_history' => ['nullable', 'string'],
            'orthodontic_history' => ['nullable', 'string'],
            'previous_surgery' => ['nullable', 'string'],
            'previous_complications' => ['nullable', 'string'],
            'dental_habits' => ['nullable', 'string'],
            'oral_hygiene' => ['nullable', 'string'],
            'chief_concerns' => ['nullable', 'string'],
        ]);
        $data['previous_extraction'] = $request->boolean('previous_extraction');
        $data['previous_root_canal'] = $request->boolean('previous_root_canal');
        $data['recorded_at'] = now();
        $data['recorded_by_user_id'] = $request->user()->id;

        $patient->dentalHistory()->updateOrCreate(['patient_id' => $patient->id], $data);

        return redirect()->route('patients.show', $patient)->with('status', 'Dental history saved.');
    }
}

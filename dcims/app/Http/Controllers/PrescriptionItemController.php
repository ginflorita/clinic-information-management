<?php

namespace App\Http\Controllers;

use App\Models\Encounter;
use App\Models\Prescription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PrescriptionItemController extends Controller
{
    public function store(Request $request, Encounter $encounter, Prescription $prescription): RedirectResponse
    {
        if ($prescription->status !== 'active') {
            throw ValidationException::withMessages([
                'medication_id' => 'Medications can only be added to an active prescription.',
            ]);
        }

        $data = $request->validate([
            'medication_id' => ['required', 'exists:medications,id'],
            'dose' => ['required', 'string', 'max:255'],
            'frequency' => ['required', 'string', 'max:255'],
            'route' => ['nullable', 'string', 'max:255'],
            'duration' => ['nullable', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'refills' => ['nullable', 'integer', 'min:0'],
            'instructions' => ['nullable', 'string'],
        ]);
        $data['refills'] = $data['refills'] ?? 0;

        $prescription->items()->create($data);

        return redirect()->route('encounters.show', $encounter)->with('status', 'Medication added.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientAddress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PatientAddressController extends Controller
{
    public function store(Request $request, Patient $patient): RedirectResponse
    {
        $data = $request->validate([
            'address_type' => ['required', 'string', 'max:50'],
            'address_line_1' => ['required', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'barangay' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:255'],
        ]);
        $data['is_primary'] = $request->boolean('is_primary');

        $patient->addresses()->create($data);

        return redirect()->route('patients.show', $patient)->with('status', 'Address added.');
    }

    public function destroy(Patient $patient, PatientAddress $address): RedirectResponse
    {
        $address->delete();

        return redirect()->route('patients.show', $patient)->with('status', 'Address removed.');
    }
}

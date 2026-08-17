<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PatientController extends Controller
{
    public function index(Request $request): View
    {
        $query = Patient::query();

        if ($search = $request->string('q')->trim()->value()) {
            $needle = '%'.strtolower($search).'%';

            $query->where(function ($q) use ($needle) {
                $q->whereRaw('LOWER(patient_number) LIKE ?', [$needle])
                    ->orWhereRaw('LOWER(first_name) LIKE ?', [$needle])
                    ->orWhereRaw('LOWER(last_name) LIKE ?', [$needle])
                    ->orWhereRaw('LOWER(email) LIKE ?', [$needle]);
            });
        }

        return view('patients.index', [
            'patients' => $query->orderBy('last_name')->get(),
            'search' => $search ?? '',
        ]);
    }

    public function create(): View
    {
        return view('patients.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatePatient($request);

        $patient = DB::transaction(fn () => Patient::create($data));

        return redirect()->route('patients.show', $patient)->with('status', 'Patient registered.');
    }

    public function show(Patient $patient): View
    {
        $patient->load(['addresses', 'contacts', 'relationships.relatedPatient', 'identifiers', 'medicalHistory', 'dentalHistory', 'conditions', 'allergies']);

        return view('patients.show', ['patient' => $patient]);
    }

    public function edit(Patient $patient): View
    {
        return view('patients.edit', ['patient' => $patient]);
    }

    public function update(Request $request, Patient $patient): RedirectResponse
    {
        $data = $this->validatePatient($request, $patient);

        $patient->update($data);

        return redirect()->route('patients.show', $patient)->with('status', 'Patient updated.');
    }

    public function archive(Patient $patient): RedirectResponse
    {
        $patient->archive();

        return redirect()->route('patients.show', $patient)->with('status', 'Patient archived.');
    }

    public function restore(Patient $patient): RedirectResponse
    {
        $patient->restore();

        return redirect()->route('patients.show', $patient)->with('status', 'Patient restored.');
    }

    private function validatePatient(Request $request, ?Patient $patient = null): array
    {
        return $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'suffix' => ['nullable', 'string', 'max:20'],
            'preferred_name' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date', 'before_or_equal:today'],
            'sex' => ['required', 'in:male,female'],
            'civil_status' => ['nullable', 'in:single,married,widowed,separated'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'registration_date' => ['required', 'date'],
            'referral_source' => ['nullable', 'string', 'max:255'],
        ]);
    }
}

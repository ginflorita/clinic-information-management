<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Medication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MedicationController extends Controller
{
    public function index(): View
    {
        return view('admin.medications.index', [
            'medications' => Medication::orderBy('generic_name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.medications.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active');

        Medication::create($data);

        return redirect()->route('admin.medications.index')->with('status', 'Medication created.');
    }

    public function edit(Medication $medication): View
    {
        return view('admin.medications.edit', ['medication' => $medication]);
    }

    public function update(Request $request, Medication $medication): RedirectResponse
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active');

        $medication->update($data);

        return redirect()->route('admin.medications.index')->with('status', 'Medication updated.');
    }

    public function destroy(Medication $medication): RedirectResponse
    {
        $medication->delete();

        return redirect()->route('admin.medications.index')->with('status', 'Medication deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'generic_name' => ['required', 'string', 'max:255'],
            'brand_name' => ['nullable', 'string', 'max:255'],
            'dosage_form' => ['nullable', 'string', 'max:255'],
            'strength' => ['nullable', 'string', 'max:255'],
            'unit' => ['nullable', 'string', 'max:255'],
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppointmentType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentTypeController extends Controller
{
    public function index(): View
    {
        return view('admin.appointment-types.index', [
            'appointmentTypes' => AppointmentType::orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.appointment-types.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'default_duration_minutes' => ['required', 'integer', 'min:5', 'max:480'],
            'color' => ['nullable', 'string', 'max:20'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        AppointmentType::create($data);

        return redirect()->route('admin.appointment-types.index')->with('status', 'Appointment type created.');
    }

    public function edit(AppointmentType $appointmentType): View
    {
        return view('admin.appointment-types.edit', ['appointmentType' => $appointmentType]);
    }

    public function update(Request $request, AppointmentType $appointmentType): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'default_duration_minutes' => ['required', 'integer', 'min:5', 'max:480'],
            'color' => ['nullable', 'string', 'max:20'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        $appointmentType->update($data);

        return redirect()->route('admin.appointment-types.index')->with('status', 'Appointment type updated.');
    }

    public function destroy(AppointmentType $appointmentType): RedirectResponse
    {
        $appointmentType->delete();

        return redirect()->route('admin.appointment-types.index')->with('status', 'Appointment type deleted.');
    }
}

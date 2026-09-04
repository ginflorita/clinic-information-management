<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AppointmentRequest;
use App\Models\AppointmentType;
use App\Models\Chair;
use App\Models\Patient;
use App\Models\Provider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use LogicException;

class AppointmentController extends Controller
{
    public function index(): View
    {
        return view('appointments.index', [
            'appointments' => Appointment::with(['patient', 'provider', 'chair', 'appointmentType', 'encounter'])
                ->orderBy('scheduled_start')
                ->get(),
        ]);
    }

    public function create(Request $request): View
    {
        $appointmentRequest = null;

        if ($requestId = $request->query('appointment_request_id')) {
            $appointmentRequest = AppointmentRequest::where('status', 'pending')->find($requestId);
        }

        return view('appointments.create', $this->formOptions() + ['appointmentRequest' => $appointmentRequest]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateAppointment($request);

        $appointmentRequest = null;
        if ($requestId = $request->input('appointment_request_id')) {
            $appointmentRequest = AppointmentRequest::where('status', 'pending')->find($requestId);
        }

        $appointment = Appointment::create($data);

        if ($appointmentRequest) {
            try {
                $appointmentRequest->confirm($appointment, $request->user());
            } catch (LogicException $e) {
                throw ValidationException::withMessages(['appointment_request_id' => $e->getMessage()]);
            }
        }

        return redirect()->route('appointments.index')->with('status', 'Appointment scheduled.');
    }

    public function edit(Appointment $appointment): View
    {
        return view('appointments.edit', $this->formOptions() + ['appointment' => $appointment]);
    }

    public function update(Request $request, Appointment $appointment): RedirectResponse
    {
        $data = $this->validateAppointment($request, $appointment);

        $appointment->update($data);

        return redirect()->route('appointments.index')->with('status', 'Appointment updated.');
    }

    public function reschedule(Request $request, Appointment $appointment): RedirectResponse
    {
        $data = $request->validate([
            'scheduled_start' => ['required', 'date'],
            'scheduled_end' => ['required', 'date', 'after:scheduled_start'],
        ]);

        $this->assertNoConflict($appointment->provider_id, $appointment->chair_id, $data['scheduled_start'], $data['scheduled_end'], $appointment->id);

        $appointment->update($data + ['status' => 'rescheduled']);

        return redirect()->route('appointments.index')->with('status', 'Appointment rescheduled.');
    }

    public function cancel(Appointment $appointment): RedirectResponse
    {
        $appointment->update(['status' => 'cancelled', 'cancelled_at' => now()]);

        return redirect()->route('appointments.index')->with('status', 'Appointment cancelled.');
    }

    public function markNoShow(Appointment $appointment): RedirectResponse
    {
        $appointment->update(['status' => 'no_show']);

        return redirect()->route('appointments.index')->with('status', 'Appointment marked as no-show.');
    }

    private function formOptions(): array
    {
        return [
            'patients' => Patient::where('status', 'active')->orderBy('last_name')->get(),
            'providers' => Provider::where('is_active', true)->orderBy('last_name')->get(),
            'appointmentTypes' => AppointmentType::where('is_active', true)->orderBy('name')->get(),
            'chairs' => Chair::where('is_active', true)->orderBy('name')->get(),
        ];
    }

    private function validateAppointment(Request $request, ?Appointment $appointment = null): array
    {
        $data = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'provider_id' => ['required', 'exists:providers,id'],
            'appointment_type_id' => ['required', 'exists:appointment_types,id'],
            'chair_id' => ['nullable', 'exists:chairs,id'],
            'scheduled_start' => ['required', 'date'],
            'scheduled_end' => ['required', 'date', 'after:scheduled_start'],
            'reason' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $this->assertNoConflict(
            $data['provider_id'],
            $data['chair_id'] ?? null,
            $data['scheduled_start'],
            $data['scheduled_end'],
            $appointment?->id
        );

        return $data;
    }

    private function assertNoConflict(int $providerId, ?int $chairId, string $start, string $end, ?int $excludeId): void
    {
        if (Appointment::hasConflict($providerId, $chairId, $start, $end, $excludeId)) {
            throw ValidationException::withMessages([
                'scheduled_start' => 'This provider or chair is already booked during that time.',
            ]);
        }
    }
}

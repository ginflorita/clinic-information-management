<?php

namespace App\Http\Controllers;

use App\Models\AppointmentRequest;
use App\Models\AppointmentType;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use LogicException;

class AppointmentRequestController extends Controller
{
    public function create(): View
    {
        return view('appointment-requests.create', [
            'appointmentTypes' => AppointmentType::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'patient_number' => ['required', 'string'],
            'date_of_birth' => ['required', 'date'],
            'appointment_type_id' => ['nullable', 'exists:appointment_types,id'],
            'preferred_date' => ['required', 'date', 'after_or_equal:today'],
            'preferred_time_period' => ['required', Rule::in(AppointmentRequest::TIME_PERIODS)],
            'reason' => ['nullable', 'string', 'max:1000'],
            'contact_phone' => ['nullable', 'required_without:contact_email', 'string', 'max:50'],
            'contact_email' => ['nullable', 'required_without:contact_phone', 'email', 'max:255'],
        ]);

        $patient = Patient::where('patient_number', $data['patient_number'])
            ->whereDate('date_of_birth', $data['date_of_birth'])
            ->first();

        if (! $patient) {
            throw ValidationException::withMessages([
                'patient_number' => 'We could not find a patient record matching that patient number and date of birth. Please contact the clinic directly.',
            ]);
        }

        $appointmentRequest = AppointmentRequest::create([
            'patient_id' => $patient->id,
            'appointment_type_id' => $data['appointment_type_id'] ?? null,
            'preferred_date' => $data['preferred_date'],
            'preferred_time_period' => $data['preferred_time_period'],
            'reason' => $data['reason'] ?? null,
            'contact_phone' => $data['contact_phone'] ?? null,
            'contact_email' => $data['contact_email'] ?? null,
        ]);

        return redirect()->route('book-appointment.create')->with(
            'status',
            "Thanks! Your request (Ref# {$appointmentRequest->reference_number}) has been received. Our staff will contact you to confirm."
        );
    }

    public function index(Request $request): View
    {
        $query = AppointmentRequest::with(['patient', 'appointmentType', 'appointment', 'reviewer'])->latest('created_at');

        if ($status = $request->string('status')->trim()->value()) {
            $query->where('status', $status);
        }

        return view('appointment-requests.index', [
            'appointmentRequests' => $query->get(),
            'statusFilter' => $status ?? '',
        ]);
    }

    public function decline(Request $request, AppointmentRequest $appointmentRequest): RedirectResponse
    {
        $data = $request->validate([
            'staff_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $appointmentRequest->decline($request->user(), $data['staff_notes'] ?? null);
        } catch (LogicException $e) {
            throw ValidationException::withMessages(['status' => $e->getMessage()]);
        }

        return redirect()->route('appointment-requests.index')->with('status', 'Appointment request declined.');
    }
}

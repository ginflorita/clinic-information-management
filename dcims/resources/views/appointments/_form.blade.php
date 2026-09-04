@csrf

@php
    $requestDefaultHour = ['morning' => 9, 'afternoon' => 13, 'evening' => 17];
    $requestDefaultStart = isset($appointmentRequest)
        ? $appointmentRequest->preferred_date->copy()->setTime($requestDefaultHour[$appointmentRequest->preferred_time_period] ?? 9, 0)
        : null;
@endphp

@isset($appointmentRequest)
    <input type="hidden" name="appointment_request_id" value="{{ $appointmentRequest->id }}">
    <div class="alert alert-info small">
        Confirming request <strong>{{ $appointmentRequest->reference_number }}</strong> — preferred
        {{ $appointmentRequest->preferred_date->format('Y-m-d') }} ({{ ucfirst($appointmentRequest->preferred_time_period) }}).
        @if ($appointmentRequest->contact_phone || $appointmentRequest->contact_email)
            Contact: {{ $appointmentRequest->contact_phone }}{{ $appointmentRequest->contact_phone && $appointmentRequest->contact_email ? ' / ' : '' }}{{ $appointmentRequest->contact_email }}.
        @endif
    </div>
@endisset

<div class="mb-3">
    <x-input-label for="patient_id" value="Patient" />
    <select id="patient_id" name="patient_id" class="form-select select2" required>
        <option value=""></option>
        @foreach ($patients as $patient)
            <option value="{{ $patient->id }}" @selected(old('patient_id', $appointment->patient_id ?? $appointmentRequest->patient_id ?? null) == $patient->id)>
                {{ $patient->full_name }} ({{ $patient->patient_number }})
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('patient_id')" class="mt-1" />
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <x-input-label for="provider_id" value="Provider" />
        <select id="provider_id" name="provider_id" class="form-select" required>
            <option value=""></option>
            @foreach ($providers as $provider)
                <option value="{{ $provider->id }}" @selected(old('provider_id', $appointment->provider_id ?? null) == $provider->id)>
                    {{ $provider->full_name }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('provider_id')" class="mt-1" />
    </div>
    <div class="col-md-6 mb-3">
        <x-input-label for="chair_id" value="Chair" />
        <select id="chair_id" name="chair_id" class="form-select">
            <option value=""></option>
            @foreach ($chairs as $chair)
                <option value="{{ $chair->id }}" @selected(old('chair_id', $appointment->chair_id ?? null) == $chair->id)>
                    {{ $chair->name }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('chair_id')" class="mt-1" />
    </div>
</div>

<div class="mb-3">
    <x-input-label for="appointment_type_id" value="Appointment Type" />
    <select id="appointment_type_id" name="appointment_type_id" class="form-select" required>
        <option value=""></option>
        @foreach ($appointmentTypes as $type)
            <option value="{{ $type->id }}" @selected(old('appointment_type_id', $appointment->appointment_type_id ?? $appointmentRequest->appointment_type_id ?? null) == $type->id)>
                {{ $type->name }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('appointment_type_id')" class="mt-1" />
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <x-input-label for="scheduled_start" value="Start" />
        <x-text-input id="scheduled_start" name="scheduled_start" type="datetime-local" :value="old('scheduled_start', isset($appointment) ? $appointment->scheduled_start->format('Y-m-d\TH:i') : ($requestDefaultStart?->format('Y-m-d\TH:i') ?? ''))" required />
        <x-input-error :messages="$errors->get('scheduled_start')" class="mt-1" />
    </div>
    <div class="col-md-6 mb-3">
        <x-input-label for="scheduled_end" value="End" />
        <x-text-input id="scheduled_end" name="scheduled_end" type="datetime-local" :value="old('scheduled_end', isset($appointment) ? $appointment->scheduled_end->format('Y-m-d\TH:i') : ($requestDefaultStart?->copy()->addMinutes(30)->format('Y-m-d\TH:i') ?? ''))" required />
        <x-input-error :messages="$errors->get('scheduled_end')" class="mt-1" />
    </div>
</div>

<div class="mb-3">
    <x-input-label for="reason" value="Reason" />
    <x-text-input id="reason" name="reason" type="text" :value="old('reason', $appointment->reason ?? $appointmentRequest->reason ?? '')" />
</div>

<div class="mb-3">
    <x-input-label for="notes" value="Notes" />
    <textarea id="notes" name="notes" class="form-control" rows="2">{{ old('notes', $appointment->notes ?? '') }}</textarea>
</div>

<div class="d-flex gap-2">
    <x-primary-button>Save</x-primary-button>
    <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('.select2').select2({ width: '100%', placeholder: 'Search patients...' });
        });
    </script>
@endpush

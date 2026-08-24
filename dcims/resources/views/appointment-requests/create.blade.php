<x-guest-layout>
    <h2 class="h4 fw-semibold mb-1">Request an Appointment</h2>
    <p class="text-secondary small mb-4">Already a patient of ours? Tell us when you'd like to come in and our staff will confirm a time with you.</p>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('book-appointment.store') }}">
        @csrf

        <div class="row">
            <div class="col-md-6 mb-3">
                <x-input-label for="patient_number" value="Patient Number" />
                <x-text-input id="patient_number" name="patient_number" type="text" :value="old('patient_number')" required autofocus placeholder="PAT-2026-000001" />
                <x-input-error :messages="$errors->get('patient_number')" class="mt-1" />
            </div>
            <div class="col-md-6 mb-3">
                <x-input-label for="date_of_birth" value="Date of Birth" />
                <x-text-input id="date_of_birth" name="date_of_birth" type="date" :value="old('date_of_birth')" required />
                <x-input-error :messages="$errors->get('date_of_birth')" class="mt-1" />
            </div>
        </div>

        <div class="mb-3">
            <x-input-label for="appointment_type_id" value="Appointment Type (optional)" />
            <select id="appointment_type_id" name="appointment_type_id" class="form-select">
                <option value="">Not sure</option>
                @foreach ($appointmentTypes as $type)
                    <option value="{{ $type->id }}" @selected(old('appointment_type_id') == $type->id)>{{ $type->name }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('appointment_type_id')" class="mt-1" />
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <x-input-label for="preferred_date" value="Preferred Date" />
                <x-text-input id="preferred_date" name="preferred_date" type="date" :value="old('preferred_date')" min="{{ now()->toDateString() }}" required />
                <x-input-error :messages="$errors->get('preferred_date')" class="mt-1" />
            </div>
            <div class="col-md-6 mb-3">
                <x-input-label for="preferred_time_period" value="Preferred Time" />
                <select id="preferred_time_period" name="preferred_time_period" class="form-select" required>
                    <option value=""></option>
                    <option value="morning" @selected(old('preferred_time_period') === 'morning')>Morning</option>
                    <option value="afternoon" @selected(old('preferred_time_period') === 'afternoon')>Afternoon</option>
                    <option value="evening" @selected(old('preferred_time_period') === 'evening')>Evening</option>
                </select>
                <x-input-error :messages="$errors->get('preferred_time_period')" class="mt-1" />
            </div>
        </div>

        <div class="mb-3">
            <x-input-label for="reason" value="Reason for Visit (optional)" />
            <textarea id="reason" name="reason" class="form-control" rows="2">{{ old('reason') }}</textarea>
            <x-input-error :messages="$errors->get('reason')" class="mt-1" />
        </div>

        <p class="small text-secondary mb-2">How should we reach you to confirm? (at least one)</p>
        <div class="row">
            <div class="col-md-6 mb-3">
                <x-input-label for="contact_phone" value="Phone" />
                <x-text-input id="contact_phone" name="contact_phone" type="text" :value="old('contact_phone')" />
                <x-input-error :messages="$errors->get('contact_phone')" class="mt-1" />
            </div>
            <div class="col-md-6 mb-3">
                <x-input-label for="contact_email" value="Email" />
                <x-text-input id="contact_email" name="contact_email" type="email" :value="old('contact_email')" />
                <x-input-error :messages="$errors->get('contact_email')" class="mt-1" />
            </div>
        </div>

        <x-primary-button class="w-100 justify-content-center">Request Appointment</x-primary-button>
    </form>

    <p class="text-secondary small text-center mt-4 mb-0">
        <a href="{{ route('login') }}" class="text-decoration-underline">Staff login</a>
    </p>
</x-guest-layout>

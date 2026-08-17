<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">New Encounter</h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4" style="max-width: 40rem;">
            <div class="bg-white shadow-sm rounded p-4">
                <form method="POST" action="{{ route('encounters.store') }}">
                    @csrf

                    <div class="mb-3">
                        <x-input-label for="patient_id" value="Patient" />
                        <select id="patient_id" name="patient_id" class="form-select select2" required>
                            <option value=""></option>
                            @foreach ($patients as $patient)
                                <option value="{{ $patient->id }}" @selected(old('patient_id') == $patient->id)>
                                    {{ $patient->full_name }} ({{ $patient->patient_number }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('patient_id')" class="mt-1" />
                    </div>

                    <div class="mb-3">
                        <x-input-label for="provider_id" value="Provider" />
                        <select id="provider_id" name="provider_id" class="form-select" required>
                            <option value=""></option>
                            @foreach ($providers as $provider)
                                <option value="{{ $provider->id }}" @selected(old('provider_id') == $provider->id)>
                                    {{ $provider->full_name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('provider_id')" class="mt-1" />
                    </div>

                    <div class="mb-3">
                        <x-input-label for="chief_complaint" value="Chief Complaint" />
                        <textarea id="chief_complaint" name="chief_complaint" class="form-control" rows="2">{{ old('chief_complaint') }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <x-primary-button>Start Encounter</x-primary-button>
                        <a href="{{ route('encounters.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                $('.select2').select2({ width: '100%', placeholder: 'Search patients...' });
            });
        </script>
    @endpush
</x-app-layout>

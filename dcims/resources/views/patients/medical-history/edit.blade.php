<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">Medical History — {{ $patient->full_name }}</h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4" style="max-width: 50rem;">
            <div class="bg-white shadow-sm rounded p-4">
                <form method="POST" action="{{ route('patients.medical-history.update', $patient) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <x-input-label for="physician_name" value="Physician Name" />
                        <x-text-input id="physician_name" name="physician_name" type="text" :value="old('physician_name', $medicalHistory->physician_name ?? '')" />
                    </div>
                    <div class="mb-3">
                        <x-input-label for="physician_contact" value="Physician Contact" />
                        <x-text-input id="physician_contact" name="physician_contact" type="text" :value="old('physician_contact', $medicalHistory->physician_contact ?? '')" />
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <x-input-label for="smoking_status" value="Smoking Status" />
                            <x-text-input id="smoking_status" name="smoking_status" type="text" :value="old('smoking_status', $medicalHistory->smoking_status ?? '')" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <x-input-label for="alcohol_use" value="Alcohol Use" />
                            <x-text-input id="alcohol_use" name="alcohol_use" type="text" :value="old('alcohol_use', $medicalHistory->alcohol_use ?? '')" />
                        </div>
                    </div>
                    <div class="mb-3">
                        <x-input-label for="pregnancy_status" value="Pregnancy Status" />
                        <x-text-input id="pregnancy_status" name="pregnancy_status" type="text" :value="old('pregnancy_status', $medicalHistory->pregnancy_status ?? '')" />
                    </div>
                    <div class="mb-3">
                        <x-input-label for="previous_surgeries" value="Previous Surgeries" />
                        <textarea id="previous_surgeries" name="previous_surgeries" class="form-control" rows="2">{{ old('previous_surgeries', $medicalHistory->previous_surgeries ?? '') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <x-input-label for="hospitalization" value="Hospitalization" />
                        <textarea id="hospitalization" name="hospitalization" class="form-control" rows="2">{{ old('hospitalization', $medicalHistory->hospitalization ?? '') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <x-input-label for="current_medications" value="Current Medications" />
                        <textarea id="current_medications" name="current_medications" class="form-control" rows="2">{{ old('current_medications', $medicalHistory->current_medications ?? '') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <x-input-label for="family_medical_history" value="Family Medical History" />
                        <textarea id="family_medical_history" name="family_medical_history" class="form-control" rows="2">{{ old('family_medical_history', $medicalHistory->family_medical_history ?? '') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <x-input-label for="medical_alerts" value="Medical Alerts" />
                        <textarea id="medical_alerts" name="medical_alerts" class="form-control" rows="2">{{ old('medical_alerts', $medicalHistory->medical_alerts ?? '') }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <x-primary-button>Save</x-primary-button>
                        <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

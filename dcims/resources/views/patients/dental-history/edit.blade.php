<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">Dental History — {{ $patient->full_name }}</h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4" style="max-width: 50rem;">
            <div class="bg-white shadow-sm rounded p-4">
                <form method="POST" action="{{ route('patients.dental-history.update', $patient) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <x-input-label for="previous_dentist" value="Previous Dentist" />
                        <x-text-input id="previous_dentist" name="previous_dentist" type="text" :value="old('previous_dentist', $dentalHistory->previous_dentist ?? '')" />
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3 form-check">
                            <input id="previous_extraction" type="checkbox" name="previous_extraction" value="1" class="form-check-input" @checked(old('previous_extraction', $dentalHistory->previous_extraction ?? false))>
                            <label class="form-check-label" for="previous_extraction">Previous Extraction</label>
                        </div>
                        <div class="col-md-6 mb-3 form-check">
                            <input id="previous_root_canal" type="checkbox" name="previous_root_canal" value="1" class="form-check-input" @checked(old('previous_root_canal', $dentalHistory->previous_root_canal ?? false))>
                            <label class="form-check-label" for="previous_root_canal">Previous Root Canal</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <x-input-label for="previous_treatments" value="Previous Treatments" />
                        <textarea id="previous_treatments" name="previous_treatments" class="form-control" rows="2">{{ old('previous_treatments', $dentalHistory->previous_treatments ?? '') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <x-input-label for="prosthetic_history" value="Prosthetic History" />
                        <textarea id="prosthetic_history" name="prosthetic_history" class="form-control" rows="2">{{ old('prosthetic_history', $dentalHistory->prosthetic_history ?? '') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <x-input-label for="orthodontic_history" value="Orthodontic History" />
                        <textarea id="orthodontic_history" name="orthodontic_history" class="form-control" rows="2">{{ old('orthodontic_history', $dentalHistory->orthodontic_history ?? '') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <x-input-label for="previous_surgery" value="Previous Surgery" />
                        <textarea id="previous_surgery" name="previous_surgery" class="form-control" rows="2">{{ old('previous_surgery', $dentalHistory->previous_surgery ?? '') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <x-input-label for="previous_complications" value="Previous Complications" />
                        <textarea id="previous_complications" name="previous_complications" class="form-control" rows="2">{{ old('previous_complications', $dentalHistory->previous_complications ?? '') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <x-input-label for="dental_habits" value="Dental Habits" />
                        <textarea id="dental_habits" name="dental_habits" class="form-control" rows="2">{{ old('dental_habits', $dentalHistory->dental_habits ?? '') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <x-input-label for="oral_hygiene" value="Oral Hygiene" />
                        <textarea id="oral_hygiene" name="oral_hygiene" class="form-control" rows="2">{{ old('oral_hygiene', $dentalHistory->oral_hygiene ?? '') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <x-input-label for="chief_concerns" value="Chief Dental Concerns" />
                        <textarea id="chief_concerns" name="chief_concerns" class="form-control" rows="2">{{ old('chief_concerns', $dentalHistory->chief_concerns ?? '') }}</textarea>
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

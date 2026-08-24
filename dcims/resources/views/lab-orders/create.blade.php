<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">New Lab Order</h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4" style="max-width: 40rem;">
            <div class="bg-white shadow-sm rounded p-4">
                <form method="POST" action="{{ route('lab-orders.store') }}">
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
                        <x-input-label for="lab_id" value="Lab" />
                        <select id="lab_id" name="lab_id" class="form-select" required>
                            <option value=""></option>
                            @foreach ($labs as $lab)
                                <option value="{{ $lab->id }}" @selected(old('lab_id') == $lab->id)>{{ $lab->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('lab_id')" class="mt-1" />
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <x-input-label for="procedure_id" value="Procedure (optional)" />
                            <select id="procedure_id" name="procedure_id" class="form-select">
                                <option value=""></option>
                                @foreach ($procedures as $procedure)
                                    <option value="{{ $procedure->id }}" @selected(old('procedure_id') == $procedure->id)>{{ $procedure->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('procedure_id')" class="mt-1" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <x-input-label for="tooth_id" value="Tooth (optional)" />
                            <select id="tooth_id" name="tooth_id" class="form-select">
                                <option value=""></option>
                                @foreach ($teeth as $tooth)
                                    <option value="{{ $tooth->id }}" @selected(old('tooth_id') == $tooth->id)>{{ $tooth->tooth_code }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('tooth_id')" class="mt-1" />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <x-input-label for="expected_date" value="Expected Date" />
                            <x-text-input id="expected_date" name="expected_date" type="date" :value="old('expected_date')" />
                            <x-input-error :messages="$errors->get('expected_date')" class="mt-1" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <x-input-label for="cost" value="Cost" />
                            <x-text-input id="cost" name="cost" type="number" step="0.01" min="0" :value="old('cost')" />
                            <x-input-error :messages="$errors->get('cost')" class="mt-1" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <x-input-label for="notes" value="Notes" />
                        <textarea id="notes" name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-1" />
                    </div>

                    <div class="d-flex gap-2">
                        <x-primary-button>Create Lab Order</x-primary-button>
                        <a href="{{ route('lab-orders.index') }}" class="btn btn-outline-secondary">Cancel</a>
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

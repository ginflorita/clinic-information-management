<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="fs-4 fw-semibold text-dark mb-0">
                {{ $patient->full_name }}
                <span class="badge {{ $patient->status === 'active' ? 'text-bg-success' : ($patient->status === 'archived' ? 'text-bg-secondary' : 'text-bg-warning') }} text-capitalize">
                    {{ $patient->status }}
                </span>
            </h2>
            <div class="d-flex gap-2">
                <a href="{{ route('patients.edit', $patient) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                @if ($patient->status === 'archived')
                    <form method="POST" action="{{ route('patients.restore', $patient) }}">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-success">Restore</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('patients.archive', $patient) }}" onsubmit="return confirm('Archive this patient?');">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-warning">Archive</button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4 d-flex flex-column gap-4" style="max-width: 60rem;">
            @if (session('status'))
                <div class="alert alert-success mb-0">{{ session('status') }}</div>
            @endif

            {{-- Allergy alert banner — must stay the first thing a clinician sees, never buried in a tab --}}
            @php $activeAllergies = $patient->allergies->where('status', 'active'); @endphp
            @if ($activeAllergies->isNotEmpty())
                <div class="alert alert-danger mb-0">
                    <strong>⚠ Allergies:</strong>
                    @foreach ($activeAllergies as $allergy)
                        <span class="badge text-bg-danger text-capitalize me-1">
                            {{ $allergy->allergen }} ({{ $allergy->severity }}{{ $allergy->reaction ? ' — '.$allergy->reaction : '' }})
                        </span>
                    @endforeach
                </div>
            @endif

            <div class="bg-white shadow-sm rounded p-4">
                <div class="row row-cols-2 row-cols-md-4 g-3">
                    <div><small class="text-secondary d-block">Patient #</small>{{ $patient->patient_number }}</div>
                    <div><small class="text-secondary d-block">Date of Birth</small>{{ $patient->date_of_birth->format('Y-m-d') }} ({{ $patient->age }})</div>
                    <div><small class="text-secondary d-block">Sex</small>{{ ucfirst($patient->sex) }}</div>
                    <div><small class="text-secondary d-block">Civil Status</small>{{ $patient->civil_status ? ucfirst($patient->civil_status) : '—' }}</div>
                    <div><small class="text-secondary d-block">Occupation</small>{{ $patient->occupation ?: '—' }}</div>
                    <div><small class="text-secondary d-block">Email</small>{{ $patient->email ?: '—' }}</div>
                    <div><small class="text-secondary d-block">Registration Date</small>{{ $patient->registration_date->format('Y-m-d') }}</div>
                    <div><small class="text-secondary d-block">Referral Source</small>{{ $patient->referral_source ?: '—' }}</div>
                </div>
            </div>

            {{-- Medical & Dental History --}}
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="bg-white shadow-sm rounded p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h3 class="fs-5 fw-medium mb-0">Medical History</h3>
                            <a href="{{ route('patients.medical-history.edit', $patient) }}" class="btn btn-sm btn-outline-secondary">
                                {{ $patient->medicalHistory ? 'Edit' : 'Record' }}
                            </a>
                        </div>
                        @if ($patient->medicalHistory)
                            <p class="small text-secondary mb-0">
                                Last recorded {{ $patient->medicalHistory->recorded_at->format('Y-m-d') }}.
                                @if ($patient->medicalHistory->medical_alerts)
                                    <span class="text-danger fw-medium d-block mt-1">Alert: {{ $patient->medicalHistory->medical_alerts }}</span>
                                @endif
                            </p>
                        @else
                            <p class="small text-secondary mb-0">No medical history on file.</p>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="bg-white shadow-sm rounded p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h3 class="fs-5 fw-medium mb-0">Dental History</h3>
                            <a href="{{ route('patients.dental-history.edit', $patient) }}" class="btn btn-sm btn-outline-secondary">
                                {{ $patient->dentalHistory ? 'Edit' : 'Record' }}
                            </a>
                        </div>
                        @if ($patient->dentalHistory)
                            <p class="small text-secondary mb-0">
                                Last recorded {{ $patient->dentalHistory->recorded_at->format('Y-m-d') }}.
                                @if ($patient->dentalHistory->chief_concerns)
                                    <span class="d-block mt-1">Chief concern: {{ $patient->dentalHistory->chief_concerns }}</span>
                                @endif
                            </p>
                        @else
                            <p class="small text-secondary mb-0">No dental history on file.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Dental Chart --}}
            <div class="bg-white shadow-sm rounded p-4 d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="fs-5 fw-medium mb-1">Dental Chart</h3>
                    <p class="small text-secondary mb-0">Tooth-by-tooth condition history, recorded per visit.</p>
                </div>
                <a href="{{ route('patients.odontogram.show', $patient) }}" class="btn btn-sm btn-outline-secondary">View Chart</a>
            </div>

            {{-- Diagnoses --}}
            <div class="bg-white shadow-sm rounded p-4">
                <h3 class="fs-5 fw-medium mb-3">Diagnoses</h3>
                <table class="table table-sm mb-0">
                    <tbody>
                        @forelse ($patient->diagnoses as $encounterDiagnosis)
                            <tr>
                                <td>
                                    <span class="badge text-bg-{{ match($encounterDiagnosis->status) { 'suspected' => 'secondary', 'active' => 'danger', 'resolved' => 'success', 'historical' => 'secondary', default => 'secondary' } }} text-capitalize">
                                        {{ $encounterDiagnosis->status }}
                                    </span>
                                </td>
                                <td>{{ $encounterDiagnosis->diagnosis->name }}</td>
                                <td>{{ $encounterDiagnosis->tooth?->tooth_code ?: '—' }}</td>
                                <td>{{ $encounterDiagnosis->diagnosed_at->format('Y-m-d') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('encounters.show', $encounterDiagnosis->encounter) }}" class="small">View encounter</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td class="text-secondary">No diagnoses on file.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Treatment Plans --}}
            <div class="bg-white shadow-sm rounded p-4">
                <h3 class="fs-5 fw-medium mb-3">Treatment Plans</h3>
                <table class="table table-sm mb-0">
                    <tbody>
                        @forelse ($patient->treatmentPlans as $plan)
                            <tr>
                                <td>{{ $plan->plan_number }}</td>
                                <td>{{ $plan->title }}</td>
                                <td>
                                    <span class="badge text-bg-{{ match($plan->status) { 'draft' => 'secondary', 'presented' => 'primary', 'accepted', 'partially_accepted' => 'info', 'completed' => 'success', 'declined', 'cancelled', 'expired' => 'danger', default => 'secondary' } }} text-capitalize">
                                        {{ str_replace('_', ' ', $plan->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('treatment-plans.show', $plan) }}" class="small">View plan</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td class="text-secondary">No treatment plans on file.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Procedures Performed --}}
            <div class="bg-white shadow-sm rounded p-4">
                <h3 class="fs-5 fw-medium mb-3">Procedures Performed</h3>
                <table class="table table-sm mb-0">
                    <tbody>
                        @forelse ($patient->procedureRecords as $record)
                            <tr>
                                <td>
                                    <span class="badge text-bg-{{ $record->status === 'completed' ? 'success' : 'secondary' }} text-capitalize">
                                        {{ $record->status }}
                                    </span>
                                </td>
                                <td>{{ $record->procedure->name }}</td>
                                <td>{{ $record->tooth?->tooth_code ?: '—' }}</td>
                                <td>{{ number_format($record->total_amount, 2) }}</td>
                                <td>{{ $record->performed_at->format('Y-m-d') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('encounters.show', $record->encounter) }}" class="small">View encounter</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td class="text-secondary">No procedures on file.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Invoices --}}
            <div class="bg-white shadow-sm rounded p-4">
                <h3 class="fs-5 fw-medium mb-3">Invoices</h3>
                <table class="table table-sm mb-0">
                    <tbody>
                        @forelse ($patient->invoices as $invoice)
                            <tr>
                                <td>{{ $invoice->invoice_number }}</td>
                                <td>{{ $invoice->invoice_date->format('Y-m-d') }}</td>
                                <td>{{ number_format($invoice->total_amount, 2) }}</td>
                                <td>
                                    <span class="{{ $invoice->balance > 0 ? 'text-danger' : 'text-success' }}">
                                        {{ number_format($invoice->balance, 2) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('invoices.show', $invoice) }}" class="small">View invoice</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td class="text-secondary">No invoices on file.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Medical Conditions --}}
            <div class="bg-white shadow-sm rounded p-4">
                <h3 class="fs-5 fw-medium mb-3">Medical Conditions</h3>
                <table class="table table-sm mb-3">
                    <tbody>
                        @forelse ($patient->conditions as $condition)
                            <tr>
                                <td>{{ $condition->condition_name }}</td>
                                <td class="text-capitalize">{{ $condition->status }}</td>
                                <td>{{ optional($condition->diagnosed_date)->format('Y-m-d') }}</td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('patients.conditions.destroy', [$patient, $condition]) }}" class="d-inline" onsubmit="return confirm('Remove this condition?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td class="text-secondary">No conditions on file.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <form method="POST" action="{{ route('patients.conditions.store', $patient) }}" class="row g-2 align-items-end">
                    @csrf
                    <div class="col-auto">
                        <input type="text" name="condition_name" class="form-control form-control-sm" placeholder="Condition (e.g. Diabetes)" required>
                    </div>
                    <div class="col-auto">
                        <select name="status" class="form-select form-select-sm">
                            <option value="active">Active</option>
                            <option value="managed">Managed</option>
                            <option value="resolved">Resolved</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="diagnosed_date" class="form-control form-control-sm">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-sm btn-outline-primary">Add Condition</button>
                    </div>
                </form>
            </div>

            {{-- Allergies --}}
            <div class="bg-white shadow-sm rounded p-4">
                <h3 class="fs-5 fw-medium mb-3">Allergies</h3>
                <table class="table table-sm mb-3">
                    <tbody>
                        @forelse ($patient->allergies as $allergy)
                            <tr>
                                <td>{{ $allergy->allergen }}</td>
                                <td>{{ $allergy->reaction }}</td>
                                <td class="text-capitalize">
                                    <span class="badge {{ $allergy->severity === 'severe' ? 'text-bg-danger' : ($allergy->severity === 'moderate' ? 'text-bg-warning' : 'text-bg-secondary') }}">
                                        {{ $allergy->severity }}
                                    </span>
                                </td>
                                <td class="text-capitalize">{{ $allergy->status }}</td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('patients.allergies.destroy', [$patient, $allergy]) }}" class="d-inline" onsubmit="return confirm('Remove this allergy?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td class="text-secondary">No allergies on file.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <form method="POST" action="{{ route('patients.allergies.store', $patient) }}" class="row g-2 align-items-end">
                    @csrf
                    <div class="col-auto">
                        <input type="text" name="allergen" class="form-control form-control-sm" placeholder="Allergen (e.g. Penicillin)" required>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="reaction" class="form-control form-control-sm" placeholder="Reaction">
                    </div>
                    <div class="col-auto">
                        <select name="severity" class="form-select form-select-sm">
                            <option value="mild">Mild</option>
                            <option value="moderate">Moderate</option>
                            <option value="severe">Severe</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="onset_date" class="form-control form-control-sm">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-sm btn-outline-primary">Add Allergy</button>
                    </div>
                </form>
            </div>

            {{-- Contacts --}}
            <div class="bg-white shadow-sm rounded p-4">
                <h3 class="fs-5 fw-medium mb-3">Contacts</h3>
                <table class="table table-sm mb-3">
                    <tbody>
                        @forelse ($patient->contacts as $contact)
                            <tr>
                                <td class="text-capitalize">{{ $contact->contact_type }}</td>
                                <td>{{ $contact->contact_value }}</td>
                                <td>{{ $contact->is_primary ? 'Primary' : '' }}</td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('patients.contacts.destroy', [$patient, $contact]) }}" class="d-inline" onsubmit="return confirm('Remove this contact?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td class="text-secondary">No contacts on file.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <form method="POST" action="{{ route('patients.contacts.store', $patient) }}" class="row g-2 align-items-end">
                    @csrf
                    <div class="col-auto">
                        <select name="contact_type" class="form-select form-select-sm" required>
                            <option value="mobile">Mobile</option>
                            <option value="telephone">Telephone</option>
                            <option value="email">Email</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="contact_value" class="form-control form-control-sm" placeholder="Value" required>
                    </div>
                    <div class="col-auto form-check">
                        <input type="checkbox" name="is_primary" value="1" class="form-check-input" id="contact_is_primary">
                        <label class="form-check-label" for="contact_is_primary">Primary</label>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-sm btn-outline-primary">Add Contact</button>
                    </div>
                </form>
            </div>

            {{-- Addresses --}}
            <div class="bg-white shadow-sm rounded p-4">
                <h3 class="fs-5 fw-medium mb-3">Addresses</h3>
                <table class="table table-sm mb-3">
                    <tbody>
                        @forelse ($patient->addresses as $address)
                            <tr>
                                <td class="text-capitalize">{{ $address->address_type }}</td>
                                <td>{{ collect([$address->address_line_1, $address->address_line_2, $address->barangay, $address->city, $address->province, $address->postal_code, $address->country])->filter()->implode(', ') }}</td>
                                <td>{{ $address->is_primary ? 'Primary' : '' }}</td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('patients.addresses.destroy', [$patient, $address]) }}" class="d-inline" onsubmit="return confirm('Remove this address?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td class="text-secondary">No addresses on file.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <form method="POST" action="{{ route('patients.addresses.store', $patient) }}" class="row g-2 align-items-end">
                    @csrf
                    <div class="col-auto">
                        <select name="address_type" class="form-select form-select-sm" required>
                            <option value="home">Home</option>
                            <option value="work">Work</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="address_line_1" class="form-control form-control-sm" placeholder="Address line 1" required>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="city" class="form-control form-control-sm" placeholder="City">
                    </div>
                    <div class="col-auto">
                        <input type="text" name="province" class="form-control form-control-sm" placeholder="Province">
                    </div>
                    <div class="col-auto form-check">
                        <input type="checkbox" name="is_primary" value="1" class="form-check-input" id="address_is_primary">
                        <label class="form-check-label" for="address_is_primary">Primary</label>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-sm btn-outline-primary">Add Address</button>
                    </div>
                </form>
            </div>

            {{-- Relationships / Guardians / Emergency Contacts --}}
            <div class="bg-white shadow-sm rounded p-4">
                <h3 class="fs-5 fw-medium mb-3">Relationships &amp; Emergency Contacts</h3>
                <table class="table table-sm mb-3">
                    <tbody>
                        @forelse ($patient->relationships as $relationship)
                            <tr>
                                <td>{{ $relationship->display_name }}</td>
                                <td class="text-capitalize">{{ $relationship->relationship_type }}</td>
                                <td>
                                    @if ($relationship->is_guardian) <span class="badge text-bg-info">Guardian</span> @endif
                                    @if ($relationship->is_emergency_contact) <span class="badge text-bg-warning">Emergency</span> @endif
                                </td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('patients.relationships.destroy', [$patient, $relationship]) }}" class="d-inline" onsubmit="return confirm('Remove this relationship?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td class="text-secondary">No relationships on file.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <form method="POST" action="{{ route('patients.relationships.store', $patient) }}" class="row g-2 align-items-end">
                    @csrf
                    <div class="col-md-4">
                        <label class="form-label small">Existing patient (if applicable)</label>
                        <select name="related_patient_id" class="form-select form-select-sm select2">
                            <option value=""></option>
                            @foreach (\App\Models\Patient::where('id', '!=', $patient->id)->orderBy('last_name')->get() as $other)
                                <option value="{{ $other->id }}">{{ $other->full_name }} ({{ $other->patient_number }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Or name (non-patient)</label>
                        <input type="text" name="contact_name" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Phone</label>
                        <input type="text" name="contact_phone" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Relationship</label>
                        <input type="text" name="relationship_type" class="form-control form-control-sm" placeholder="mother, spouse..." required>
                    </div>
                    <div class="col-auto form-check">
                        <input type="checkbox" name="is_guardian" value="1" class="form-check-input" id="is_guardian">
                        <label class="form-check-label" for="is_guardian">Guardian</label>
                    </div>
                    <div class="col-auto form-check">
                        <input type="checkbox" name="is_emergency_contact" value="1" class="form-check-input" id="is_emergency_contact">
                        <label class="form-check-label" for="is_emergency_contact">Emergency</label>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-sm btn-outline-primary">Add</button>
                    </div>
                </form>
                @error('contact_name')
                    <p class="text-danger small mt-2">{{ $message }}</p>
                @enderror
            </div>

            {{-- Identifiers --}}
            <div class="bg-white shadow-sm rounded p-4">
                <h3 class="fs-5 fw-medium mb-3">Identifiers</h3>
                <table class="table table-sm mb-3">
                    <tbody>
                        @forelse ($patient->identifiers as $identifier)
                            <tr>
                                <td class="text-capitalize">{{ $identifier->identifier_type }}</td>
                                <td>{{ $identifier->identifier_value }}</td>
                                <td>{{ $identifier->issuing_authority }}</td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('patients.identifiers.destroy', [$patient, $identifier]) }}" class="d-inline" onsubmit="return confirm('Remove this identifier?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td class="text-secondary">No identifiers on file.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <form method="POST" action="{{ route('patients.identifiers.store', $patient) }}" class="row g-2 align-items-end">
                    @csrf
                    <div class="col-auto">
                        <input type="text" name="identifier_type" class="form-control form-control-sm" placeholder="philhealth, national_id..." required>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="identifier_value" class="form-control form-control-sm" placeholder="Value" required>
                    </div>
                    <div class="col-auto">
                        <input type="text" name="issuing_authority" class="form-control form-control-sm" placeholder="Issuing authority">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-sm btn-outline-primary">Add Identifier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                $('.select2').select2({ width: '100%', allowClear: true, placeholder: 'Search patients...' });
            });
        </script>
    @endpush
</x-app-layout>

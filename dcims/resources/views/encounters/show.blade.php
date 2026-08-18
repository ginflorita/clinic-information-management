<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="fs-4 fw-semibold text-dark mb-0">
                Encounter {{ $encounter->encounter_number }}
                <span class="badge text-bg-{{ $encounter->status === 'completed' ? 'success' : 'primary' }} text-capitalize">
                    {{ str_replace('_', ' ', $encounter->status) }}
                </span>
            </h2>
            <div class="d-flex gap-2">
                @if ($uninvoicedCompletedProcedures > 0)
                    <form method="POST" action="{{ route('encounters.invoice.generate', $encounter) }}">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-primary">Generate Invoice ({{ $uninvoicedCompletedProcedures }})</button>
                    </form>
                @endif
                @if ($encounter->status !== 'completed')
                    <form method="POST" action="{{ route('encounters.complete', $encounter) }}" onsubmit="return confirm('Complete this encounter?');">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-success">Complete Encounter</button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4 d-flex flex-column gap-4" style="max-width: 50rem;">
            @if (session('status'))
                <div class="alert alert-success mb-0">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger mb-0">{{ $errors->first() }}</div>
            @endif

            <div class="bg-white shadow-sm rounded p-4">
                <div class="row row-cols-2 row-cols-md-4 g-3">
                    <div><small class="text-secondary d-block">Patient</small>
                        <a href="{{ route('patients.show', $encounter->patient) }}">{{ $encounter->patient->full_name }}</a>
                    </div>
                    <div><small class="text-secondary d-block">Provider</small>{{ $encounter->provider->full_name }}</div>
                    <div><small class="text-secondary d-block">Started</small>{{ $encounter->started_at->format('Y-m-d H:i') }}</div>
                    <div><small class="text-secondary d-block">Ended</small>{{ optional($encounter->ended_at)->format('Y-m-d H:i') ?: '—' }}</div>
                </div>
                @if ($encounter->chief_complaint)
                    <p class="mt-3 mb-0"><strong>Chief complaint:</strong> {{ $encounter->chief_complaint }}</p>
                @endif
                @if ($encounter->invoices->isNotEmpty())
                    <div class="mt-3 pt-3 border-top">
                        <small class="text-secondary d-block mb-1">Invoices</small>
                        @foreach ($encounter->invoices as $invoice)
                            <a href="{{ route('invoices.show', $invoice) }}" class="badge text-bg-light text-dark border me-1">
                                {{ $invoice->invoice_number }} — Balance {{ number_format($invoice->balance, 2) }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="bg-white shadow-sm rounded p-4">
                <h3 class="fs-5 fw-medium mb-3">Clinical Notes</h3>

                <div class="d-flex flex-column gap-3 mb-4">
                    @forelse ($encounter->clinicalNotes as $note)
                        <div class="border rounded p-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <span class="badge text-bg-{{ match($note->status) { 'draft' => 'secondary', 'signed' => 'success', 'amended' => 'warning', default => 'secondary' } }} text-capitalize">
                                        {{ $note->status }}
                                    </span>
                                    <span class="text-capitalize fw-medium ms-1">{{ $note->note_type }}</span>
                                    <small class="text-secondary d-block">
                                        by {{ $note->creator->name }} on {{ $note->created_at->format('Y-m-d H:i') }}
                                        @if ($note->signed_at)
                                            — signed by {{ $note->signer->name }} on {{ $note->signed_at->format('Y-m-d H:i') }}
                                        @endif
                                    </small>
                                </div>
                                <div class="d-flex gap-1">
                                    @if ($note->status === 'draft')
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#edit-note-{{ $note->id }}">Edit</button>
                                        <form method="POST" action="{{ route('encounters.notes.sign', [$encounter, $note]) }}" onsubmit="return confirm('Sign this note? It cannot be edited afterward.');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success">Sign</button>
                                        </form>
                                    @elseif ($note->status === 'signed')
                                        <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="collapse" data-bs-target="#amend-note-{{ $note->id }}">Amend</button>
                                    @endif
                                </div>
                            </div>

                            <p class="mt-2 mb-0" style="white-space: pre-wrap;">{{ $note->note_text }}</p>

                            @if ($note->status === 'amended')
                                <p class="text-secondary small mt-2 mb-0">Superseded by an amendment below.</p>
                            @endif

                            @if ($note->amendment_reason)
                                <p class="text-secondary small mt-2 mb-0"><strong>Amendment reason:</strong> {{ $note->amendment_reason }}</p>
                            @endif

                            @if ($note->status === 'draft')
                                <div class="collapse mt-3" id="edit-note-{{ $note->id }}">
                                    <form method="POST" action="{{ route('encounters.notes.update', [$encounter, $note]) }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="note_type" value="{{ $note->note_type }}">
                                        <textarea name="note_text" class="form-control mb-2" rows="3">{{ $note->note_text }}</textarea>
                                        <x-primary-button>Save</x-primary-button>
                                    </form>
                                </div>
                            @endif

                            @if ($note->status === 'signed')
                                <div class="collapse mt-3" id="amend-note-{{ $note->id }}">
                                    <form method="POST" action="{{ route('encounters.notes.amend', [$encounter, $note]) }}">
                                        @csrf
                                        <textarea name="note_text" class="form-control mb-2" rows="3" placeholder="Corrected text">{{ $note->note_text }}</textarea>
                                        <input type="text" name="amendment_reason" class="form-control mb-2" placeholder="Reason for amendment" required>
                                        <x-primary-button>Save Amendment</x-primary-button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-secondary">No clinical notes yet.</p>
                    @endforelse
                </div>

                <form method="POST" action="{{ route('encounters.notes.store', $encounter) }}">
                    @csrf
                    <div class="row g-2">
                        <div class="col-auto">
                            <select name="note_type" class="form-select form-select-sm">
                                <option value="progress">Progress</option>
                                <option value="soap">SOAP</option>
                                <option value="examination">Examination</option>
                                <option value="procedure">Procedure</option>
                                <option value="follow_up">Follow-up</option>
                            </select>
                        </div>
                        <div class="col">
                            <textarea name="note_text" class="form-control form-control-sm" rows="2" placeholder="New note..." required></textarea>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-sm btn-outline-primary">Add Note</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow-sm rounded p-4">
                <h3 class="fs-5 fw-medium mb-3">Diagnoses</h3>

                <div class="d-flex flex-column gap-2 mb-3">
                    @forelse ($encounter->diagnoses as $encounterDiagnosis)
                        <div class="border rounded p-2 d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge text-bg-{{ match($encounterDiagnosis->status) { 'suspected' => 'secondary', 'active' => 'danger', 'resolved' => 'success', 'historical' => 'secondary', default => 'secondary' } }} text-capitalize">
                                    {{ $encounterDiagnosis->status }}
                                </span>
                                <span class="fw-medium ms-1">{{ $encounterDiagnosis->diagnosis->name }}</span>
                                @if ($encounterDiagnosis->tooth)
                                    <span class="text-secondary">— Tooth {{ $encounterDiagnosis->tooth->tooth_code }}</span>
                                @endif
                                @if ($encounterDiagnosis->notes)
                                    <div class="text-secondary small">{{ $encounterDiagnosis->notes }}</div>
                                @endif
                            </div>
                            <form method="POST" action="{{ route('encounters.diagnoses.status', [$encounter, $encounterDiagnosis]) }}" class="d-flex gap-1">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="form-select form-select-sm">
                                    @foreach ($diagnosisStatuses as $status)
                                        <option value="{{ $status }}" @selected($status === $encounterDiagnosis->status)>{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-sm btn-outline-secondary">Update</button>
                            </form>
                        </div>
                    @empty
                        <p class="text-secondary mb-0">No diagnoses recorded for this encounter.</p>
                    @endforelse
                </div>

                <form method="POST" action="{{ route('encounters.diagnoses.store', $encounter) }}">
                    @csrf
                    <div class="row g-2">
                        <div class="col-md-5">
                            <select name="diagnosis_id" class="form-select form-select-sm" required>
                                <option value="">Diagnosis...</option>
                                @foreach ($diagnosisOptions as $diagnosis)
                                    <option value="{{ $diagnosis->id }}">{{ $diagnosis->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="tooth_id" class="form-select form-select-sm">
                                <option value="">Tooth (optional)...</option>
                                @foreach ($teeth as $tooth)
                                    <option value="{{ $tooth->id }}">{{ $tooth->tooth_code }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="notes" class="form-control form-control-sm" placeholder="Notes (optional)">
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-sm btn-outline-primary w-100">Add</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow-sm rounded p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="fs-5 fw-medium mb-0">Dental Chart</h3>
                    <a href="{{ route('patients.odontogram.show', $encounter->patient) }}" class="small">View full chart history</a>
                </div>

                <p class="text-secondary small mb-2">Click a tooth to record a condition.</p>
                <div class="d-flex flex-wrap gap-1 mb-3">
                    @foreach ($teeth as $tooth)
                        <button type="button" class="btn btn-sm btn-outline-dark" style="width: 3rem;" data-bs-toggle="collapse" data-bs-target="#tooth-form-{{ $tooth->id }}">
                            {{ $tooth->tooth_code }}
                        </button>
                    @endforeach
                </div>

                @foreach ($teeth as $tooth)
                    <div class="collapse mb-2" id="tooth-form-{{ $tooth->id }}">
                        <form method="POST" action="{{ route('encounters.odontogram-entries.store', $encounter) }}" class="border rounded p-3">
                            @csrf
                            <input type="hidden" name="tooth_id" value="{{ $tooth->id }}">
                            <p class="fw-medium mb-2 small">Tooth {{ $tooth->tooth_code }} — {{ $tooth->tooth_name }}</p>
                            <div class="row g-2 align-items-start">
                                <div class="col-md-4">
                                    <select name="condition_id" class="form-select form-select-sm" required>
                                        <option value="">Condition...</option>
                                        @foreach ($toothConditions as $condition)
                                            <option value="{{ $condition->id }}">{{ $condition->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <div class="d-flex flex-wrap gap-2 pt-1">
                                        @foreach ($surfaces as $surface)
                                            <div class="form-check form-check-inline m-0">
                                                <input class="form-check-input" type="checkbox" name="surfaces[]" value="{{ $surface }}" id="surface-{{ $tooth->id }}-{{ $surface }}">
                                                <label class="form-check-label small" for="surface-{{ $tooth->id }}-{{ $surface }}">{{ $surface }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-sm btn-outline-primary w-100">Record</button>
                                </div>
                            </div>
                            <textarea name="notes" class="form-control form-control-sm mt-2" rows="1" placeholder="Notes (optional)"></textarea>
                        </form>
                    </div>
                @endforeach

                <h4 class="fs-6 fw-medium mt-4 mb-2">Recorded This Visit</h4>
                @forelse (($encounter->odontogram->entries ?? []) as $entry)
                    <div class="border rounded p-2 mb-2 small">
                        <strong>{{ $entry->tooth->tooth_code }}</strong> {{ $entry->condition->name }}
                        @if ($entry->surfaces->isNotEmpty())
                            <span class="text-secondary">({{ $entry->surfaces->pluck('surface')->join(', ') }})</span>
                        @endif
                        @if ($entry->notes)
                            <div class="text-secondary">{{ $entry->notes }}</div>
                        @endif
                    </div>
                @empty
                    <p class="text-secondary small mb-0">No chart entries recorded this visit yet.</p>
                @endforelse
            </div>

            <div class="bg-white shadow-sm rounded p-4">
                <h3 class="fs-5 fw-medium mb-3">Procedures Performed</h3>

                <div class="d-flex flex-column gap-2 mb-3">
                    @forelse ($encounter->procedureRecords as $record)
                        <div class="border rounded p-2 d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge text-bg-{{ $record->status === 'completed' ? 'success' : 'secondary' }} text-capitalize">
                                    {{ $record->status }}
                                </span>
                                <span class="fw-medium ms-1">{{ $record->procedure->name }}</span>
                                @if ($record->tooth)
                                    <span class="text-secondary">— Tooth {{ $record->tooth->tooth_code }}</span>
                                @endif
                                <span class="text-secondary">× {{ $record->quantity }} @ {{ number_format($record->unit_price, 2) }} = {{ number_format($record->total_amount, 2) }}</span>
                                @if ($record->treatmentPlanItem)
                                    <div class="text-secondary small">From plan item: {{ $record->treatmentPlanItem->procedure->name ?? '—' }}</div>
                                @endif
                                @if ($record->notes)
                                    <div class="text-secondary small">{{ $record->notes }}</div>
                                @endif
                            </div>
                            @if ($record->status === 'completed')
                                <form method="POST" action="{{ route('encounters.procedure-records.void', [$encounter, $record]) }}" onsubmit="return confirm('Void this procedure record?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Void</button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <p class="text-secondary mb-0">No procedures recorded for this encounter.</p>
                    @endforelse
                </div>

                <form method="POST" action="{{ route('encounters.procedure-records.store', $encounter) }}">
                    @csrf
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <x-input-label for="procedure_record_procedure_id" value="Procedure" />
                            <select id="procedure_record_procedure_id" name="procedure_id" class="form-select form-select-sm" required>
                                <option value="">Procedure...</option>
                                @foreach ($procedures as $procedure)
                                    <option value="{{ $procedure->id }}">{{ $procedure->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <x-input-label for="procedure_record_tooth_id" value="Tooth" />
                            <select id="procedure_record_tooth_id" name="tooth_id" class="form-select form-select-sm">
                                <option value="">Optional...</option>
                                @foreach ($teeth as $tooth)
                                    <option value="{{ $tooth->id }}">{{ $tooth->tooth_code }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <x-input-label for="procedure_record_plan_item" value="From Plan Item" />
                            <select id="procedure_record_plan_item" name="treatment_plan_item_id" class="form-select form-select-sm">
                                <option value="">None...</option>
                                @foreach ($outstandingPlanItems as $planItem)
                                    <option value="{{ $planItem->id }}">
                                        {{ $planItem->treatmentPlan->plan_number }}: {{ $planItem->procedure->name }}
                                        @if ($planItem->tooth) (Tooth {{ $planItem->tooth->tooth_code }}) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1">
                            <x-input-label for="procedure_record_quantity" value="Qty" />
                            <input type="number" id="procedure_record_quantity" name="quantity" class="form-control form-control-sm" value="1" min="1" required>
                        </div>
                        <div class="col-md-2">
                            <x-input-label for="procedure_record_unit_price" value="Unit Price" />
                            <input type="number" id="procedure_record_unit_price" name="unit_price" class="form-control form-control-sm" step="0.01" min="0" placeholder="From procedure">
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-sm btn-outline-primary w-100">Add</button>
                        </div>
                    </div>
                    <textarea name="notes" class="form-control form-control-sm mt-2" rows="1" placeholder="Notes (optional)"></textarea>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

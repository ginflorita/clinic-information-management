<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="fs-4 fw-semibold text-dark mb-0">
                Encounter {{ $encounter->encounter_number }}
                <span class="badge text-bg-{{ $encounter->status === 'completed' ? 'success' : 'primary' }} text-capitalize">
                    {{ str_replace('_', ' ', $encounter->status) }}
                </span>
            </h2>
            @if ($encounter->status !== 'completed')
                <form method="POST" action="{{ route('encounters.complete', $encounter) }}" onsubmit="return confirm('Complete this encounter?');">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-success">Complete Encounter</button>
                </form>
            @endif
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
        </div>
    </div>
</x-app-layout>

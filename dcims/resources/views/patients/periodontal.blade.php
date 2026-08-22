<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">
            Periodontal Chart — <a href="{{ route('patients.show', $patient) }}">{{ $patient->full_name }}</a>
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4" style="max-width: 60rem;">
            <div class="bg-white shadow-sm rounded p-4">
                @if ($examinations->isEmpty())
                    <p class="text-secondary mb-0">No periodontal exams recorded yet. Record findings from an encounter's Periodontal Chart section.</p>
                @else
                    @foreach ($examinations as $examination)
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <strong>{{ $examination->examined_at->format('Y-m-d') }}</strong>
                                <span class="text-secondary small">{{ $examination->examiner->name }}</span>
                            </div>

                            @forelse ($examination->toothRecords as $record)
                                <div class="small border-top pt-2 mt-2">
                                    <strong>Tooth {{ $record->tooth->tooth_code }}</strong>
                                    @if ($record->mobility !== null)
                                        <span class="text-secondary">Mobility: {{ $record->mobility }}</span>
                                    @endif
                                    @if ($record->furcation !== null)
                                        <span class="text-secondary">Furcation: {{ $record->furcation }}</span>
                                    @endif
                                    <div class="text-secondary">
                                        @foreach ($record->measurements as $measurement)
                                            {{ ucfirst($measurement->site) }}: PD {{ $measurement->probing_depth }}
                                            @if ($measurement->gingival_recession !== null) / Rec {{ $measurement->gingival_recession }} @endif
                                            @if ($measurement->clinical_attachment_level !== null) / CAL {{ $measurement->clinical_attachment_level }} @endif
                                            @if ($measurement->bleeding_on_probing) (BOP) @endif
                                            @if ($measurement->plaque_present) (Plaque) @endif
                                            @if (! $loop->last) &middot; @endif
                                        @endforeach
                                    </div>
                                    @if ($record->notes)
                                        <div class="text-secondary">{{ $record->notes }}</div>
                                    @endif
                                </div>
                            @empty
                                <p class="text-secondary small mb-0">No teeth recorded in this exam.</p>
                            @endforelse
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

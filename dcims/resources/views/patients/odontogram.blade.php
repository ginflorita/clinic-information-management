<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">
            Dental Chart — <a href="{{ route('patients.show', $patient) }}">{{ $patient->full_name }}</a>
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4" style="max-width: 60rem;">
            <div class="bg-white shadow-sm rounded p-4">
                @if ($entriesByTooth->isEmpty())
                    <p class="text-secondary mb-0">No chart entries recorded yet. Record findings from an encounter's Dental Chart section.</p>
                @else
                    @foreach ($entriesByTooth as $toothCode => $entries)
                        @php $current = $entries->last(); @endphp
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>Tooth {{ $toothCode }}</strong>
                                    <span class="text-secondary">{{ $current->tooth->tooth_name }}</span>
                                </div>
                                <span class="badge text-bg-primary text-capitalize">Current: {{ $current->condition->name }}</span>
                            </div>

                            <div class="mt-2 small">
                                @foreach ($entries as $entry)
                                    <div class="d-flex justify-content-between border-top pt-1 mt-1">
                                        <div>
                                            {{ $entry->condition->name }}
                                            @if ($entry->surfaces->isNotEmpty())
                                                <span class="text-secondary">({{ $entry->surfaces->pluck('surface')->join(', ') }})</span>
                                            @endif
                                            @if ($entry->notes)
                                                <div class="text-secondary">{{ $entry->notes }}</div>
                                            @endif
                                        </div>
                                        <div class="text-secondary text-nowrap ms-3">
                                            {{ $entry->odontogram->recorded_at->format('Y-m-d') }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

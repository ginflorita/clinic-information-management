<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">
            Prescriptions — <a href="{{ route('patients.show', $patient) }}">{{ $patient->full_name }}</a>
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4" style="max-width: 60rem;">
            <div class="bg-white shadow-sm rounded p-4">
                @if ($prescriptions->isEmpty())
                    <p class="text-secondary mb-0">No prescriptions recorded yet. Issue one from an encounter's Prescriptions section.</p>
                @else
                    @foreach ($prescriptions as $prescription)
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="badge text-bg-{{ $prescription->status === 'active' ? 'success' : 'secondary' }} text-capitalize">
                                        {{ $prescription->status }}
                                    </span>
                                    <span class="fw-medium ms-1">{{ $prescription->prescription_number }}</span>
                                </div>
                                <div class="text-secondary small text-end">
                                    {{ $prescription->prescribed_at->format('Y-m-d') }}<br>
                                    {{ $prescription->provider->full_name ?? '—' }}
                                </div>
                            </div>

                            @if ($prescription->notes)
                                <p class="text-secondary small mb-2">{{ $prescription->notes }}</p>
                            @endif

                            @forelse ($prescription->items as $item)
                                <div class="small border-top pt-2 mt-2">
                                    <strong>{{ $item->medication->generic_name }}</strong>
                                    @if ($item->medication->brand_name)
                                        <span class="text-secondary">({{ $item->medication->brand_name }})</span>
                                    @endif
                                    <span class="text-secondary">
                                        — {{ $item->dose }}, {{ $item->frequency }}
                                        @if ($item->route) via {{ $item->route }} @endif
                                        @if ($item->duration) for {{ $item->duration }} @endif
                                        &middot; Qty {{ $item->quantity }} &middot; Refills {{ $item->refills }}
                                    </span>
                                    @if ($item->instructions)
                                        <div class="text-secondary">{{ $item->instructions }}</div>
                                    @endif
                                </div>
                            @empty
                                <p class="text-secondary small mb-0">No medications recorded on this prescription.</p>
                            @endforelse
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">
            Timeline — <a href="{{ route('patients.show', $patient) }}">{{ $patient->full_name }}</a>
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4" style="max-width: 45rem;">
            <div class="bg-white shadow-sm rounded p-4">
                @if ($entries->isEmpty())
                    <p class="text-secondary mb-0">No activity on file.</p>
                @else
                    <div class="d-flex flex-column">
                        @foreach ($entries as $entry)
                            <div class="d-flex gap-3 pb-3 {{ ! $loop->last ? 'border-start ms-2 ps-4' : 'ms-2 ps-4' }}" style="border-color: #dee2e6 !important;">
                                <div style="margin-left: -2.15rem;">
                                    <div class="rounded-circle bg-primary" style="width: 0.6rem; height: 0.6rem; margin-top: 0.4rem;"></div>
                                </div>
                                <div>
                                    <small class="text-secondary d-block">{{ $entry['datetime']->format('Y-m-d H:i') }}</small>
                                    <span>{{ $entry['description'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

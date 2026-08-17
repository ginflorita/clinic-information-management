<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">Encounters</h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded p-4">
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('encounters.create') }}" class="btn btn-primary">New Encounter</a>
                </div>

                <table id="data-table" class="table table-striped align-middle w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Patient</th>
                            <th>Provider</th>
                            <th>Started</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($encounters as $encounter)
                            <tr>
                                <td>{{ $encounter->encounter_number }}</td>
                                <td>{{ $encounter->patient->full_name }}</td>
                                <td>{{ $encounter->provider->full_name }}</td>
                                <td>{{ $encounter->started_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <span class="badge text-bg-{{ $encounter->status === 'completed' ? 'success' : 'primary' }} text-capitalize">
                                        {{ str_replace('_', ' ', $encounter->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('encounters.show', $encounter) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                new DataTable('#data-table', { order: [[3, 'desc']] });
            });
        </script>
    @endpush
</x-app-layout>

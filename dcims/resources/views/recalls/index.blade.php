<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">Recalls</h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded p-4">
                <table id="data-table" class="table table-striped align-middle w-100">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Recall Type</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Notes</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recalls as $recall)
                            <tr>
                                <td><a href="{{ route('patients.show', $recall->patient) }}">{{ $recall->patient->full_name }}</a></td>
                                <td>{{ $recall->recallType->name }}</td>
                                <td>{{ $recall->due_date->format('Y-m-d') }}</td>
                                <td>
                                    <span class="badge {{ $recall->status === 'completed' ? 'text-bg-success' : ($recall->isOverdue() ? 'text-bg-danger' : ($recall->status === 'cancelled' ? 'text-bg-secondary' : 'text-bg-warning')) }} text-capitalize">
                                        {{ $recall->isOverdue() ? 'Overdue' : $recall->status }}
                                    </span>
                                </td>
                                <td>{{ $recall->notes }}</td>
                                <td class="text-end">
                                    @if ($recall->status === 'pending')
                                        <form method="POST" action="{{ route('recalls.complete', $recall) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success">Complete</button>
                                        </form>
                                        <form method="POST" action="{{ route('recalls.cancel', $recall) }}" class="d-inline" onsubmit="return confirm('Cancel this recall?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Cancel</button>
                                        </form>
                                    @endif
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
                new DataTable('#data-table', { order: [[2, 'asc']] });
            });
        </script>
    @endpush
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">Patient Queue — {{ now()->format('Y-m-d') }}</h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4" style="max-width: 50rem;">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded p-4">
                <table class="table table-sm mb-3">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Patient</th>
                            <th>Status</th>
                            <th>Checked In</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($queueEntries as $entry)
                            <tr>
                                <td>{{ $entry->queue_number }}</td>
                                <td>{{ $entry->patient->full_name }}</td>
                                <td>
                                    <span class="badge text-bg-{{ match($entry->status) {
                                        'waiting' => 'secondary',
                                        'called' => 'info',
                                        'in_treatment' => 'warning',
                                        default => 'primary',
                                    } }} text-capitalize">
                                        {{ str_replace('_', ' ', $entry->status) }}
                                    </span>
                                </td>
                                <td>{{ $entry->checked_in_at->format('H:i') }}</td>
                                <td class="text-end text-nowrap">
                                    @if ($entry->status === 'waiting')
                                        <form action="{{ route('queue.call', $entry) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-primary">Call</button>
                                        </form>
                                    @elseif ($entry->status === 'called')
                                        <form action="{{ route('queue.start', $entry) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-primary">Start Treatment</button>
                                        </form>
                                    @elseif ($entry->status === 'in_treatment')
                                        <form action="{{ route('queue.complete', $entry) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success">Complete</button>
                                        </form>
                                    @endif
                                    <form action="{{ route('queue.skip', $entry) }}" method="POST" class="d-inline" onsubmit="return confirm('Skip this patient?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-secondary">Skip</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-secondary">No one in the queue today.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <form method="POST" action="{{ route('queue.store') }}" class="row g-2 align-items-end">
                    @csrf
                    <div class="col-auto" style="min-width: 20rem;">
                        <select name="patient_id" class="form-select select2" required>
                            <option value=""></option>
                            @foreach ($patients as $patient)
                                <option value="{{ $patient->id }}">{{ $patient->full_name }} ({{ $patient->patient_number }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-sm btn-outline-primary">Add to Queue</button>
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

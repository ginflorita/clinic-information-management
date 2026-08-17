<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">Appointments</h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded p-4">
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('appointments.create') }}" class="btn btn-primary">New Appointment</a>
                </div>

                <table id="data-table" class="table table-striped align-middle w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Patient</th>
                            <th>Provider</th>
                            <th>Chair</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($appointments as $appointment)
                            <tr>
                                <td>{{ $appointment->appointment_number }}</td>
                                <td>{{ $appointment->patient->full_name }}</td>
                                <td>{{ $appointment->provider->full_name }}</td>
                                <td>{{ $appointment->chair?->name }}</td>
                                <td>{{ $appointment->scheduled_start->format('Y-m-d H:i') }}</td>
                                <td>{{ $appointment->scheduled_end->format('H:i') }}</td>
                                <td>
                                    <span class="badge text-bg-{{ match(true) {
                                        in_array($appointment->status, ['cancelled', 'no_show']) => 'secondary',
                                        $appointment->status === 'completed' => 'success',
                                        default => 'primary',
                                    } }} text-capitalize">
                                        {{ str_replace('_', ' ', $appointment->status) }}
                                    </span>
                                </td>
                                <td class="text-end text-nowrap">
                                    @if ($appointment->encounter)
                                        <a href="{{ route('encounters.show', $appointment->encounter) }}" class="btn btn-sm btn-outline-primary">View Encounter</a>
                                    @elseif (! in_array($appointment->status, ['cancelled', 'no_show']))
                                        <form action="{{ route('appointments.encounter.start', $appointment) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-primary">Start Encounter</button>
                                        </form>
                                    @endif
                                    @if (! in_array($appointment->status, ['cancelled', 'no_show', 'completed']))
                                        <a href="{{ route('appointments.edit', $appointment) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                        <form action="{{ route('appointments.no-show', $appointment) }}" method="POST" class="d-inline" onsubmit="return confirm('Mark this appointment as a no-show?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-warning">No-show</button>
                                        </form>
                                        <form action="{{ route('appointments.cancel', $appointment) }}" method="POST" class="d-inline" onsubmit="return confirm('Cancel this appointment?');">
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
                new DataTable('#data-table', { order: [[4, 'asc']] });
            });
        </script>
    @endpush
</x-app-layout>

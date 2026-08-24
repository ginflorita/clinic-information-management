<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">Appointment Requests</h2>
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
                            <th>Ref #</th>
                            <th>Patient</th>
                            <th>Type</th>
                            <th>Preferred</th>
                            <th>Contact</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($appointmentRequests as $appointmentRequest)
                            <tr>
                                <td>{{ $appointmentRequest->reference_number }}</td>
                                <td><a href="{{ route('patients.show', $appointmentRequest->patient) }}">{{ $appointmentRequest->patient->full_name }}</a></td>
                                <td>{{ $appointmentRequest->appointmentType->name ?? '—' }}</td>
                                <td>{{ $appointmentRequest->preferred_date->format('Y-m-d') }} ({{ ucfirst($appointmentRequest->preferred_time_period) }})</td>
                                <td>{{ $appointmentRequest->contact_phone }}{{ $appointmentRequest->contact_phone && $appointmentRequest->contact_email ? ' / ' : '' }}{{ $appointmentRequest->contact_email }}</td>
                                <td>{{ $appointmentRequest->reason }}</td>
                                <td>
                                    <span class="badge text-bg-{{ match($appointmentRequest->status) {
                                        'confirmed' => 'success',
                                        'declined' => 'secondary',
                                        default => 'warning',
                                    } }} text-capitalize">
                                        {{ $appointmentRequest->status }}
                                    </span>
                                </td>
                                <td class="text-end text-nowrap">
                                    @if ($appointmentRequest->status === 'pending')
                                        <a href="{{ route('appointments.create', ['appointment_request_id' => $appointmentRequest->id]) }}" class="btn btn-sm btn-outline-success">Confirm</a>
                                        <form action="{{ route('appointment-requests.decline', $appointmentRequest) }}" method="POST" class="d-inline" onsubmit="return confirm('Decline this appointment request?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Decline</button>
                                        </form>
                                    @elseif ($appointmentRequest->status === 'confirmed' && $appointmentRequest->appointment)
                                        <a href="{{ route('appointments.index') }}" class="btn btn-sm btn-outline-primary">View Appointment</a>
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
                new DataTable('#data-table', { order: [[0, 'desc']] });
            });
        </script>
    @endpush
</x-app-layout>

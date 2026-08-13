<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">Patients</h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded p-4">
                <div class="d-flex justify-content-between mb-3 gap-2">
                    <form method="GET" class="d-flex gap-2" style="max-width: 24rem;">
                        <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="Search by name, number, or email">
                        <button type="submit" class="btn btn-outline-secondary">Search</button>
                    </form>
                    <a href="{{ route('patients.create') }}" class="btn btn-primary">Register Patient</a>
                </div>

                <table id="data-table" class="table table-striped align-middle w-100">
                    <thead>
                        <tr>
                            <th>Patient #</th>
                            <th>Name</th>
                            <th>Date of Birth</th>
                            <th>Sex</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($patients as $patient)
                            <tr>
                                <td>{{ $patient->patient_number }}</td>
                                <td>{{ $patient->last_name }}, {{ $patient->first_name }}</td>
                                <td>{{ $patient->date_of_birth->format('Y-m-d') }}</td>
                                <td class="text-capitalize">{{ $patient->sex }}</td>
                                <td>
                                    <span class="badge {{ $patient->status === 'active' ? 'text-bg-success' : ($patient->status === 'archived' ? 'text-bg-secondary' : 'text-bg-warning') }} text-capitalize">
                                        {{ $patient->status }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('patients.show', $patient) }}" class="btn btn-sm btn-outline-secondary">View</a>
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
                new DataTable('#data-table', { paging: {{ $patients->count() > 10 ? 'true' : 'false' }} });
            });
        </script>
    @endpush
</x-app-layout>

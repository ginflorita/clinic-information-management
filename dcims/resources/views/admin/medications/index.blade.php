<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">Medications</h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded p-4">
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('admin.medications.create') }}" class="btn btn-primary">New Medication</a>
                </div>

                <table id="data-table" class="table table-striped align-middle w-100">
                    <thead>
                        <tr>
                            <th>Generic Name</th>
                            <th>Brand Name</th>
                            <th>Dosage Form</th>
                            <th>Strength</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($medications as $medication)
                            <tr>
                                <td>{{ $medication->generic_name }}</td>
                                <td>{{ $medication->brand_name }}</td>
                                <td>{{ $medication->dosage_form }}</td>
                                <td>{{ $medication->strength }} {{ $medication->unit }}</td>
                                <td>
                                    <span class="badge {{ $medication->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                        {{ $medication->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.medications.edit', $medication) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                    <form action="{{ route('admin.medications.destroy', $medication) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this medication?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
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
                new DataTable('#data-table');
            });
        </script>
    @endpush
</x-app-layout>

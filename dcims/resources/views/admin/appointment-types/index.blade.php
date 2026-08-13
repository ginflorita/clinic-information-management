<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">Appointment Types</h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded p-4">
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('admin.appointment-types.create') }}" class="btn btn-primary">New Appointment Type</a>
                </div>

                <table id="data-table" class="table table-striped align-middle w-100">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Default Duration</th>
                            <th>Color</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($appointmentTypes as $type)
                            <tr>
                                <td>{{ $type->name }}</td>
                                <td>{{ $type->default_duration_minutes }} min</td>
                                <td>
                                    @if ($type->color)
                                        <span class="d-inline-block rounded-circle border" style="width:1rem;height:1rem;background-color: {{ $type->color }};"></span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $type->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                        {{ $type->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.appointment-types.edit', $type) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                    <form action="{{ route('admin.appointment-types.destroy', $type) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this appointment type?');">
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

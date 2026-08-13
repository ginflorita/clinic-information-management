<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">Procedures</h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded p-4">
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('admin.procedures.create') }}" class="btn btn-primary">New Procedure</a>
                </div>

                <table id="data-table" class="table table-striped align-middle w-100">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Default Fee</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($procedures as $procedure)
                            <tr>
                                <td>{{ $procedure->code }}</td>
                                <td>{{ $procedure->name }}</td>
                                <td>{{ $procedure->category?->name }}</td>
                                <td>{{ number_format($procedure->default_fee, 2) }}</td>
                                <td>{{ $procedure->default_duration_minutes }} min</td>
                                <td>
                                    <span class="badge {{ $procedure->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                        {{ $procedure->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.procedures.edit', $procedure) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                    <form action="{{ route('admin.procedures.destroy', $procedure) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this procedure?');">
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

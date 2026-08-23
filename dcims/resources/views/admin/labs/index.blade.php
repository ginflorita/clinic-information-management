<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">Labs</h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded p-4">
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('admin.labs.create') }}" class="btn btn-primary">New Lab</a>
                </div>

                <table id="data-table" class="table table-striped align-middle w-100">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Contact Person</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($labs as $lab)
                            <tr>
                                <td>{{ $lab->name }}</td>
                                <td>{{ $lab->contact_person }}</td>
                                <td>{{ $lab->phone }}</td>
                                <td>{{ $lab->email }}</td>
                                <td>
                                    <span class="badge {{ $lab->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                        {{ $lab->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.labs.edit', $lab) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                    <form action="{{ route('admin.labs.destroy', $lab) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this lab?');">
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

<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">Products</h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded p-4">
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">New Product</a>
                </div>

                <table id="data-table" class="table table-striped align-middle w-100">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Unit</th>
                            <th>Reorder Level</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td>{{ $product->sku }}</td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->category?->name }}</td>
                                <td>{{ $product->unit?->name }}</td>
                                <td>{{ number_format($product->reorder_level, 2) }}</td>
                                <td>
                                    <span class="badge {{ $product->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this product?');">
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

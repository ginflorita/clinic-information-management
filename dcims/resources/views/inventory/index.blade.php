<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">Inventory</h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="row row-cols-2 row-cols-md-4 g-3 mb-4">
                <div class="col">
                    <div class="bg-white shadow-sm rounded p-3">
                        <small class="text-secondary d-block">Products Tracked</small>
                        <span class="fs-4 fw-semibold">{{ $products->count() }}</span>
                    </div>
                </div>
                <div class="col">
                    <div class="bg-white shadow-sm rounded p-3">
                        <small class="text-secondary d-block">Low Stock</small>
                        <span class="fs-4 fw-semibold {{ $lowStockCount > 0 ? 'text-danger' : '' }}">{{ $lowStockCount }}</span>
                    </div>
                </div>
                <div class="col">
                    <div class="bg-white shadow-sm rounded p-3">
                        <small class="text-secondary d-block">Expiring within {{ $expiryWarningDays }} days</small>
                        <span class="fs-4 fw-semibold {{ $expiringBatchCount > 0 ? 'text-danger' : '' }}">{{ $expiringBatchCount }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded p-4">
                <table id="data-table" class="table table-striped align-middle w-100">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>On Hand</th>
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
                                <td>{{ number_format($product->current_stock, 2) }} {{ $product->unit?->abbreviation }}</td>
                                <td>{{ number_format($product->reorder_level, 2) }}</td>
                                <td>
                                    @if ($product->isLowStock())
                                        <span class="badge text-bg-danger">Low Stock</span>
                                    @else
                                        <span class="badge text-bg-success">OK</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('inventory.show', $product) }}" class="btn btn-sm btn-outline-secondary">View</a>
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

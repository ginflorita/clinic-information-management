<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">
            <a href="{{ route('reports.index') }}" class="text-secondary text-decoration-none">Reports</a> / Inventory
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4">
            @include('reports._date-range')

            <div class="row row-cols-2 row-cols-md-4 g-3 mb-4">
                <div class="col">
                    <div class="bg-white shadow-sm rounded p-3">
                        <small class="text-secondary d-block">Active Products</small>
                        <span class="fs-4 fw-semibold">{{ $products->count() }}</span>
                    </div>
                </div>
                <div class="col">
                    <div class="bg-white shadow-sm rounded p-3">
                        <small class="text-secondary d-block">Low Stock</small>
                        <span class="fs-4 fw-semibold">{{ $lowStockProducts->count() }}</span>
                    </div>
                </div>
                <div class="col">
                    <div class="bg-white shadow-sm rounded p-3">
                        <small class="text-secondary d-block">Expiring Soon</small>
                        <span class="fs-4 fw-semibold">{{ $expiringBatches->count() }}</span>
                    </div>
                </div>
                <div class="col">
                    <div class="bg-white shadow-sm rounded p-3">
                        <small class="text-secondary d-block">Purchases Received</small>
                        <span class="fs-4 fw-semibold">{{ number_format($purchasesReceived ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="bg-white shadow-sm rounded p-4">
                        <h3 class="fs-6 fw-medium mb-3">Low Stock Items</h3>
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="text-end">Current Stock</th>
                                    <th class="text-end">Reorder Level</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($lowStockProducts as $product)
                                    <tr>
                                        <td><a href="{{ route('inventory.show', $product) }}">{{ $product->name }}</a></td>
                                        <td class="text-end">{{ number_format($product->current_stock, 2) }}</td>
                                        <td class="text-end">{{ $product->reorder_level }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-secondary">Nothing is low on stock.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="bg-white shadow-sm rounded p-4">
                        <h3 class="fs-6 fw-medium mb-3">Expiring Batches</h3>
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Batch</th>
                                    <th class="text-end">Expires</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($expiringBatches as $batch)
                                    <tr>
                                        <td>{{ $batch->product->name }}</td>
                                        <td>{{ $batch->batch_number }}</td>
                                        <td class="text-end">{{ $batch->expiry_date->format('Y-m-d') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-secondary">Nothing expiring soon.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded p-4 mt-3">
                <h3 class="fs-6 fw-medium mb-3">Stock Movement</h3>
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th class="text-end">Movements</th>
                            <th class="text-end">Net Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($movementCounts as $row)
                            <tr>
                                <td class="text-capitalize">{{ str_replace('_', ' ', $row->movement_type) }}</td>
                                <td class="text-end">{{ $row->total }}</td>
                                <td class="text-end">{{ $row->quantity }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-secondary">No stock movement in this range.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

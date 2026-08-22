<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">Purchase Orders</h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded p-4">
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary">New Purchase Order</a>
                </div>

                <table id="data-table" class="table table-striped align-middle w-100">
                    <thead>
                        <tr>
                            <th>PO #</th>
                            <th>Supplier</th>
                            <th>Order Date</th>
                            <th>Expected</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($purchaseOrders as $po)
                            <tr>
                                <td>{{ $po->po_number }}</td>
                                <td>{{ $po->supplier->name }}</td>
                                <td>{{ $po->order_date->format('Y-m-d') }}</td>
                                <td>{{ $po->expected_date?->format('Y-m-d') ?? '—' }}</td>
                                <td>
                                    <span class="badge text-bg-{{ match($po->status) { 'draft' => 'secondary', 'ordered' => 'primary', 'partially_received' => 'info', 'received' => 'success', 'cancelled' => 'danger', default => 'secondary' } }} text-capitalize">
                                        {{ str_replace('_', ' ', $po->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('purchase-orders.show', $po) }}" class="btn btn-sm btn-outline-secondary">View</a>
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
                new DataTable('#data-table', { order: [[2, 'desc']] });
            });
        </script>
    @endpush
</x-app-layout>

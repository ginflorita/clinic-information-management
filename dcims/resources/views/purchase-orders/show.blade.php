<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="fs-4 fw-semibold text-dark mb-0">
                {{ $purchaseOrder->po_number }}
                <span class="badge text-bg-{{ match($purchaseOrder->status) { 'draft' => 'secondary', 'ordered' => 'primary', 'partially_received' => 'info', 'received' => 'success', 'cancelled' => 'danger', default => 'secondary' } }} text-capitalize">
                    {{ str_replace('_', ' ', $purchaseOrder->status) }}
                </span>
            </h2>
            <div class="d-flex gap-2">
                @foreach ($availableTransitions as $nextStatus)
                    <form method="POST" action="{{ route('purchase-orders.transition', $purchaseOrder) }}" onsubmit="return confirm('Mark this purchase order as {{ str_replace('_', ' ', $nextStatus) }}?');">
                        @csrf
                        <input type="hidden" name="status" value="{{ $nextStatus }}">
                        <button type="submit" class="btn btn-sm btn-outline-primary text-capitalize">{{ str_replace('_', ' ', $nextStatus) }}</button>
                    </form>
                @endforeach
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4 d-flex flex-column gap-4" style="max-width: 60rem;">
            @if (session('status'))
                <div class="alert alert-success mb-0">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger mb-0">{{ $errors->first() }}</div>
            @endif

            <div class="bg-white shadow-sm rounded p-4">
                <div class="row row-cols-2 row-cols-md-4 g-3">
                    <div><small class="text-secondary d-block">Supplier</small>{{ $purchaseOrder->supplier->name }}</div>
                    <div><small class="text-secondary d-block">Order Date</small>{{ $purchaseOrder->order_date->format('Y-m-d') }}</div>
                    <div><small class="text-secondary d-block">Expected</small>{{ $purchaseOrder->expected_date?->format('Y-m-d') ?? '—' }}</div>
                    <div><small class="text-secondary d-block">Created By</small>{{ $purchaseOrder->creator?->name ?? '—' }}</div>
                </div>
                @if ($purchaseOrder->notes)
                    <p class="mt-3 mb-0"><strong>Notes:</strong> {{ $purchaseOrder->notes }}</p>
                @endif
            </div>

            <div class="bg-white shadow-sm rounded p-4">
                <h3 class="fs-5 fw-medium mb-3">Line Items</h3>

                <table class="table table-sm align-middle mb-4">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Ordered</th>
                            <th>Received</th>
                            <th>Remaining</th>
                            <th>Unit Cost</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($purchaseOrder->items as $item)
                            <tr>
                                <td>{{ $item->product->name }}</td>
                                <td>{{ number_format($item->quantity_ordered, 2) }}</td>
                                <td>{{ number_format($item->quantity_received, 2) }}</td>
                                <td>{{ number_format($item->remainingQuantity(), 2) }}</td>
                                <td>{{ number_format($item->unit_cost, 2) }}</td>
                                <td>{{ number_format($item->quantity_ordered * $item->unit_cost, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-secondary">No items yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if ($purchaseOrder->status === 'draft')
                    <form method="POST" action="{{ route('purchase-orders.items.store', $purchaseOrder) }}">
                        @csrf
                        <div class="row g-2 align-items-end">
                            <div class="col-md-5">
                                <x-input-label for="product_id" value="Product" />
                                <select id="product_id" name="product_id" class="form-select form-select-sm" required>
                                    <option value="">Select...</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <x-input-label for="quantity_ordered" value="Quantity" />
                                <input type="number" id="quantity_ordered" name="quantity_ordered" class="form-control form-control-sm" step="0.01" min="0.01" required>
                            </div>
                            <div class="col-md-2">
                                <x-input-label for="unit_cost" value="Unit Cost" />
                                <input type="number" id="unit_cost" name="unit_cost" class="form-control form-control-sm" step="0.01" min="0" required>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-sm btn-outline-primary w-100">Add Item</button>
                            </div>
                        </div>
                    </form>
                @endif
            </div>

            @if (in_array($purchaseOrder->status, ['ordered', 'partially_received'], true))
                <div class="bg-white shadow-sm rounded p-4">
                    <h3 class="fs-5 fw-medium mb-3">Receive Goods</h3>
                    <form method="POST" action="{{ route('purchase-orders.receipts.store', $purchaseOrder) }}">
                        @csrf
                        <table class="table table-sm align-middle mb-3">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Remaining</th>
                                    <th style="width: 10rem;">Receive Now</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchaseOrder->items as $item)
                                    @if ($item->remainingQuantity() > 0)
                                        <tr>
                                            <td>{{ $item->product->name }}</td>
                                            <td>{{ number_format($item->remainingQuantity(), 2) }}</td>
                                            <td>
                                                <input type="number" name="items[{{ $item->id }}]" class="form-control form-control-sm" step="0.01" min="0" max="{{ $item->remainingQuantity() }}">
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                        <div class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <x-input-label for="received_date" value="Received Date" />
                                <input type="date" id="received_date" name="received_date" class="form-control form-control-sm" value="{{ now()->toDateString() }}" required>
                            </div>
                            <div class="col-md-6">
                                <x-input-label for="receipt_notes" value="Notes" />
                                <input type="text" id="receipt_notes" name="notes" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-sm btn-outline-primary w-100">Record Receipt</button>
                            </div>
                        </div>
                    </form>
                </div>
            @endif

            <div class="bg-white shadow-sm rounded p-4">
                <h3 class="fs-5 fw-medium mb-3">Goods Receipts</h3>
                @forelse ($purchaseOrder->goodsReceipts as $receipt)
                    <div class="border rounded p-2 mb-2">
                        <div class="d-flex justify-content-between">
                            <span class="fw-medium">{{ $receipt->receipt_number }}</span>
                            <span class="text-secondary">{{ $receipt->received_date->format('Y-m-d') }} — {{ $receipt->receivedByUser?->name ?? 'System' }}</span>
                        </div>
                        <ul class="list-unstyled mb-0 small text-secondary">
                            @foreach ($receipt->items as $item)
                                <li>{{ $item->batch->product->name }}: +{{ number_format($item->quantity_received, 2) }}</li>
                            @endforeach
                        </ul>
                        @if ($receipt->notes)
                            <div class="small text-secondary">{{ $receipt->notes }}</div>
                        @endif
                    </div>
                @empty
                    <p class="text-secondary mb-0">No goods received yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var select = document.getElementById('product_id');
                if (select && window.$) {
                    $(select).select2({ width: '100%', dropdownParent: select.closest('form') });
                }
            });
        </script>
    @endpush
</x-app-layout>

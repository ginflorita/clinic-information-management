<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">
            {{ $product->name }}
            <span class="badge text-bg-light text-dark border">{{ $product->sku }}</span>
            @if ($product->isLowStock())
                <span class="badge text-bg-danger">Low Stock</span>
            @endif
        </h2>
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
                    <div><small class="text-secondary d-block">Category</small>{{ $product->category?->name ?? '—' }}</div>
                    <div><small class="text-secondary d-block">Unit</small>{{ $product->unit?->name ?? '—' }}</div>
                    <div><small class="text-secondary d-block">Reorder Level</small>{{ number_format($product->reorder_level, 2) }}</div>
                    <div><small class="text-secondary d-block">On Hand</small><strong>{{ number_format($product->batches->sum('quantity'), 2) }}</strong></div>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded p-4">
                <h3 class="fs-5 fw-medium mb-3">Batches</h3>
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Batch #</th>
                            <th>Lot #</th>
                            <th>Supplier</th>
                            <th>Expiry</th>
                            <th>Qty</th>
                            <th>Unit Cost</th>
                            <th>Received</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($batches as $batch)
                            <tr>
                                <td>{{ $batch->batch_number ?: '—' }}</td>
                                <td>{{ $batch->lot_number ?: '—' }}</td>
                                <td>{{ $batch->supplier?->name ?? '—' }}</td>
                                <td>
                                    @if ($batch->expiry_date)
                                        <span class="{{ $batch->isExpiringWithin($expiryWarningDays) ? 'text-danger fw-medium' : '' }}">
                                            {{ $batch->expiry_date->format('Y-m-d') }}
                                        </span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{{ number_format($batch->quantity, 2) }}</td>
                                <td>{{ $batch->unit_cost !== null ? number_format($batch->unit_cost, 2) : '—' }}</td>
                                <td>{{ $batch->received_at->format('Y-m-d') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-secondary">No stock received yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="bg-white shadow-sm rounded p-4">
                <h3 class="fs-5 fw-medium mb-3">Receive Stock</h3>
                <form method="POST" action="{{ route('inventory.batches.store', $product) }}">
                    @csrf
                    <div class="row g-2 align-items-end">
                        <div class="col-md-2">
                            <x-input-label for="supplier_id" value="Supplier" />
                            <select id="supplier_id" name="supplier_id" class="form-select form-select-sm">
                                <option value="">—</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <x-input-label for="batch_number" value="Batch #" />
                            <input type="text" id="batch_number" name="batch_number" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <x-input-label for="lot_number" value="Lot #" />
                            <input type="text" id="lot_number" name="lot_number" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <x-input-label for="expiry_date" value="Expiry" />
                            <input type="date" id="expiry_date" name="expiry_date" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-1">
                            <x-input-label for="quantity" value="Qty" />
                            <input type="number" id="quantity" name="quantity" class="form-control form-control-sm" step="0.01" min="0.01" required>
                        </div>
                        <div class="col-md-1">
                            <x-input-label for="unit_cost" value="Cost" />
                            <input type="number" id="unit_cost" name="unit_cost" class="form-control form-control-sm" step="0.01" min="0">
                        </div>
                        <div class="col-md-1">
                            <x-input-label for="received_at" value="Received" />
                            <input type="date" id="received_at" name="received_at" class="form-control form-control-sm" value="{{ now()->toDateString() }}" required>
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-sm btn-outline-primary w-100">Receive</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="row row-cols-1 row-cols-md-2 g-4">
                <div class="col">
                    <div class="bg-white shadow-sm rounded p-4 h-100">
                        <h3 class="fs-5 fw-medium mb-3">Record Usage</h3>
                        <form method="POST" action="{{ route('inventory.stock-out', $product) }}">
                            @csrf
                            <div class="mb-2">
                                <x-input-label for="stock_out_batch_id" value="Batch" />
                                <select id="stock_out_batch_id" name="batch_id" class="form-select form-select-sm" required>
                                    <option value="">Select a batch...</option>
                                    @foreach ($batches->where('quantity', '>', 0) as $batch)
                                        <option value="{{ $batch->id }}">{{ $batch->batch_number ?: ('Batch #'.$batch->id) }} ({{ number_format($batch->quantity, 2) }} available)</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <x-input-label for="stock_out_quantity" value="Quantity Used" />
                                <input type="number" id="stock_out_quantity" name="quantity" class="form-control form-control-sm" step="0.01" min="0.01" required>
                            </div>
                            <div class="mb-2">
                                <x-input-label for="stock_out_notes" value="Notes" />
                                <input type="text" id="stock_out_notes" name="notes" class="form-control form-control-sm">
                            </div>
                            <button type="submit" class="btn btn-sm btn-outline-primary">Record Usage</button>
                        </form>
                    </div>
                </div>
                <div class="col">
                    <div class="bg-white shadow-sm rounded p-4 h-100">
                        <h3 class="fs-5 fw-medium mb-3">Adjust Stock</h3>
                        <form method="POST" action="{{ route('inventory.adjust', $product) }}">
                            @csrf
                            <div class="mb-2">
                                <x-input-label for="adjust_batch_id" value="Batch" />
                                <select id="adjust_batch_id" name="batch_id" class="form-select form-select-sm" required>
                                    <option value="">Select a batch...</option>
                                    @foreach ($batches as $batch)
                                        <option value="{{ $batch->id }}">{{ $batch->batch_number ?: ('Batch #'.$batch->id) }} ({{ number_format($batch->quantity, 2) }} on hand)</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <x-input-label for="delta" value="Adjustment (+/-)" />
                                <input type="number" id="delta" name="delta" class="form-control form-control-sm" step="0.01" required>
                            </div>
                            <div class="mb-2">
                                <x-input-label for="adjust_notes" value="Reason" />
                                <input type="text" id="adjust_notes" name="notes" class="form-control form-control-sm" required>
                            </div>
                            <button type="submit" class="btn btn-sm btn-outline-secondary">Adjust</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded p-4">
                <h3 class="fs-5 fw-medium mb-3">Recent Movements</h3>
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Batch</th>
                            <th>Qty</th>
                            <th>By</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($movements as $movement)
                            <tr>
                                <td>{{ $movement->movement_date->format('Y-m-d') }}</td>
                                <td>
                                    <span class="badge text-bg-{{ $movement->movement_type === 'stock_in' ? 'success' : ($movement->movement_type === 'stock_out' ? 'primary' : 'secondary') }} text-capitalize">
                                        {{ str_replace('_', ' ', $movement->movement_type) }}
                                    </span>
                                </td>
                                <td>{{ $movement->batch->batch_number ?: ('#'.$movement->batch_id) }}</td>
                                <td class="{{ $movement->quantity < 0 ? 'text-danger' : 'text-success' }}">{{ $movement->quantity > 0 ? '+' : '' }}{{ number_format($movement->quantity, 2) }}</td>
                                <td>{{ $movement->performedByUser?->name ?? 'System' }}</td>
                                <td class="text-secondary small">{{ $movement->notes }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-secondary">No movements recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

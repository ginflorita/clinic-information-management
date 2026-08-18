<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">
            {{ $invoice->invoice_number }}
            <span class="badge text-bg-{{ $invoice->status === 'cancelled' ? 'secondary' : ($invoice->balance <= 0 ? 'success' : 'primary') }} text-capitalize">
                {{ $invoice->status === 'cancelled' ? 'cancelled' : ($invoice->balance <= 0 ? 'paid' : 'outstanding') }}
            </span>
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4 d-flex flex-column gap-4" style="max-width: 55rem;">
            @if (session('status'))
                <div class="alert alert-success mb-0">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger mb-0">{{ $errors->first() }}</div>
            @endif

            <div class="bg-white shadow-sm rounded p-4">
                <div class="row row-cols-2 row-cols-md-4 g-3 mb-3">
                    <div><small class="text-secondary d-block">Patient</small>
                        <a href="{{ route('patients.show', $invoice->patient) }}">{{ $invoice->patient->full_name }}</a>
                    </div>
                    <div><small class="text-secondary d-block">Invoice Date</small>{{ $invoice->invoice_date->format('Y-m-d') }}</div>
                    @if ($invoice->encounter)
                        <div><small class="text-secondary d-block">Encounter</small>
                            <a href="{{ route('encounters.show', $invoice->encounter) }}">{{ $invoice->encounter->encounter_number }}</a>
                        </div>
                    @endif
                </div>

                <div class="row row-cols-2 row-cols-md-3 g-3 border-top pt-3">
                    <div><small class="text-secondary d-block">Subtotal</small>{{ number_format($invoice->subtotal, 2) }}</div>
                    <div><small class="text-secondary d-block">Discount</small>{{ number_format($invoice->discount_amount, 2) }}</div>
                    <div><small class="text-secondary d-block">Tax</small>{{ number_format($invoice->tax_amount, 2) }}</div>
                    <div><small class="text-secondary d-block">Total</small><strong>{{ number_format($invoice->total_amount, 2) }}</strong></div>
                    <div><small class="text-secondary d-block">Paid</small>{{ number_format($invoice->amount_paid, 2) }}</div>
                    <div><small class="text-secondary d-block">Balance</small><strong class="{{ $invoice->balance > 0 ? 'text-danger' : 'text-success' }}">{{ number_format($invoice->balance, 2) }}</strong></div>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded p-4">
                <h3 class="fs-5 fw-medium mb-3">Line Items</h3>
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Qty</th>
                            <th>Unit Price</th>
                            <th>Discount</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoice->items as $item)
                            <tr>
                                <td>{{ $item->description }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->unit_price, 2) }}</td>
                                <td>{{ number_format($item->discount_amount, 2) }}</td>
                                <td>{{ number_format($item->total_amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="bg-white shadow-sm rounded p-4">
                <h3 class="fs-5 fw-medium mb-3">Payments</h3>
                <div class="d-flex flex-column gap-2 mb-3">
                    @forelse ($invoice->payments as $payment)
                        <div class="border rounded p-2 d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge text-bg-{{ $payment->status === 'completed' ? 'success' : 'secondary' }} text-capitalize">
                                    {{ $payment->status }}
                                </span>
                                <span class="fw-medium ms-1">{{ number_format($payment->amount, 2) }}</span>
                                <span class="text-secondary">via {{ $payment->paymentMethod->name }} on {{ $payment->payment_date->format('Y-m-d') }}</span>
                                @if ($payment->reference_number)
                                    <span class="text-secondary">(Ref: {{ $payment->reference_number }})</span>
                                @endif
                            </div>
                            @if ($payment->status === 'completed')
                                <form method="POST" action="{{ route('invoices.payments.void', [$invoice, $payment]) }}" onsubmit="return confirm('Void this payment?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Void</button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <p class="text-secondary mb-0">No payments recorded yet.</p>
                    @endforelse
                </div>

                <form method="POST" action="{{ route('invoices.payments.store', $invoice) }}">
                    @csrf
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <x-input-label for="payment_method_id" value="Method" />
                            <select id="payment_method_id" name="payment_method_id" class="form-select form-select-sm" required>
                                <option value="">Select...</option>
                                @foreach ($paymentMethods as $method)
                                    <option value="{{ $method->id }}">{{ $method->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <x-input-label for="amount" value="Amount" />
                            <input type="number" id="amount" name="amount" class="form-control form-control-sm" step="0.01" min="0.01" required>
                        </div>
                        <div class="col-md-3">
                            <x-input-label for="reference_number" value="Reference #" />
                            <input type="text" id="reference_number" name="reference_number" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-sm btn-outline-primary w-100">Record Payment</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow-sm rounded p-4">
                <h3 class="fs-5 fw-medium mb-3">Adjustments</h3>
                <div class="d-flex flex-column gap-2 mb-3">
                    @forelse ($invoice->adjustments as $adjustment)
                        <div class="border rounded p-2">
                            <span class="fw-medium text-capitalize">{{ str_replace('_', ' ', $adjustment->type) }}</span>
                            <span class="ms-1">-{{ number_format($adjustment->amount, 2) }}</span>
                            <span class="text-secondary">by {{ $adjustment->creator->name }} on {{ $adjustment->created_at->format('Y-m-d') }}</span>
                            @if ($adjustment->reason)
                                <div class="text-secondary small">{{ $adjustment->reason }}</div>
                            @endif
                        </div>
                    @empty
                        <p class="text-secondary mb-0">No adjustments recorded.</p>
                    @endforelse
                </div>

                <form method="POST" action="{{ route('invoices.adjustments.store', $invoice) }}">
                    @csrf
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <x-input-label for="type" value="Type" />
                            <select id="type" name="type" class="form-select form-select-sm">
                                @foreach ($adjustmentTypes as $type)
                                    <option value="{{ $type }}">{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <x-input-label for="adjustment_amount" value="Amount" />
                            <input type="number" id="adjustment_amount" name="amount" class="form-control form-control-sm" step="0.01" min="0.01" required>
                        </div>
                        <div class="col-md-5">
                            <x-input-label for="reason" value="Reason" />
                            <input type="text" id="reason" name="reason" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-sm btn-outline-primary w-100">Add Adjustment</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

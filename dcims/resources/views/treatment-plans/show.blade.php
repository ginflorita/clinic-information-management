<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="fs-4 fw-semibold text-dark mb-0">
                {{ $treatmentPlan->plan_number }} — {{ $treatmentPlan->title }}
                <span class="badge text-bg-{{ match($treatmentPlan->status) { 'draft' => 'secondary', 'presented' => 'primary', 'accepted', 'partially_accepted' => 'info', 'completed' => 'success', 'declined', 'cancelled', 'expired' => 'danger', default => 'secondary' } }} text-capitalize">
                    {{ str_replace('_', ' ', $treatmentPlan->status) }}
                </span>
            </h2>
            <div class="d-flex gap-2">
                @foreach ($availableTransitions as $nextStatus)
                    <form method="POST" action="{{ route('treatment-plans.transition', $treatmentPlan) }}" onsubmit="return confirm('Mark this plan as {{ str_replace('_', ' ', $nextStatus) }}?');">
                        @csrf
                        <input type="hidden" name="status" value="{{ $nextStatus }}">
                        <button type="submit" class="btn btn-sm btn-outline-primary text-capitalize">{{ str_replace('_', ' ', $nextStatus) }}</button>
                    </form>
                @endforeach
            </div>
        </div>
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
                <div class="row row-cols-2 row-cols-md-4 g-3">
                    <div><small class="text-secondary d-block">Patient</small>
                        <a href="{{ route('patients.show', $treatmentPlan->patient) }}">{{ $treatmentPlan->patient->full_name }}</a>
                    </div>
                    <div><small class="text-secondary d-block">Provider</small>{{ $treatmentPlan->provider->full_name }}</div>
                    <div><small class="text-secondary d-block">Presented</small>{{ optional($treatmentPlan->presented_at)->format('Y-m-d H:i') ?: '—' }}</div>
                    <div><small class="text-secondary d-block">Accepted</small>{{ optional($treatmentPlan->accepted_at)->format('Y-m-d H:i') ?: '—' }}</div>
                </div>
                @if ($treatmentPlan->notes)
                    <p class="mt-3 mb-0"><strong>Notes:</strong> {{ $treatmentPlan->notes }}</p>
                @endif
            </div>

            <div class="bg-white shadow-sm rounded p-4">
                <h3 class="fs-5 fw-medium mb-3">Line Items</h3>

                <table class="table table-sm align-middle mb-4">
                    <thead>
                        <tr>
                            <th>Procedure</th>
                            <th>Tooth</th>
                            <th>Qty</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($treatmentPlan->items as $item)
                            <tr>
                                <td>{{ $item->procedure->name }}</td>
                                <td>{{ $item->tooth?->tooth_code ?: '—' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->estimated_unit_price, 2) }}</td>
                                <td>{{ number_format($item->estimated_total, 2) }}</td>
                                <td class="text-capitalize">{{ $item->priority ?: '—' }}</td>
                                <td>
                                    <span class="badge text-bg-{{ match($item->status) { 'proposed' => 'secondary', 'accepted' => 'info', 'completed' => 'success', 'declined' => 'danger', default => 'secondary' } }} text-capitalize">
                                        {{ $item->status }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('treatment-plans.items.status', [$treatmentPlan, $item]) }}" class="d-flex gap-1">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" class="form-select form-select-sm">
                                            @foreach ($itemStatuses as $status)
                                                <option value="{{ $status }}" @selected($status === $item->status)>{{ ucfirst($status) }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-outline-secondary">Update</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-secondary">No items on this plan yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <form method="POST" action="{{ route('treatment-plans.items.store', $treatmentPlan) }}">
                    @csrf
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <x-input-label for="procedure_id" value="Procedure" />
                            <select id="procedure_id" name="procedure_id" class="form-select form-select-sm" required>
                                <option value="">Procedure...</option>
                                @foreach ($procedures as $procedure)
                                    <option value="{{ $procedure->id }}" data-fee="{{ $procedure->default_fee }}">{{ $procedure->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <x-input-label for="tooth_id" value="Tooth" />
                            <select id="tooth_id" name="tooth_id" class="form-select form-select-sm">
                                <option value="">Optional...</option>
                                @foreach ($teeth as $tooth)
                                    <option value="{{ $tooth->id }}">{{ $tooth->tooth_code }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1">
                            <x-input-label for="quantity" value="Qty" />
                            <input type="number" id="quantity" name="quantity" class="form-control form-control-sm" value="1" min="1" required>
                        </div>
                        <div class="col-md-2">
                            <x-input-label for="estimated_unit_price" value="Unit Price" />
                            <input type="number" id="estimated_unit_price" name="estimated_unit_price" class="form-control form-control-sm" step="0.01" min="0" placeholder="From procedure">
                        </div>
                        <div class="col-md-2">
                            <x-input-label for="priority" value="Priority" />
                            <input type="text" id="priority" name="priority" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-sm btn-outline-primary w-100">Add Item</button>
                        </div>
                    </div>
                    <textarea name="notes" class="form-control form-control-sm mt-2" rows="1" placeholder="Notes (optional)"></textarea>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.getElementById('procedure_id').addEventListener('change', function (e) {
                    var fee = e.target.selectedOptions[0]?.dataset.fee;
                    var priceInput = document.getElementById('estimated_unit_price');
                    if (fee && !priceInput.value) {
                        priceInput.placeholder = fee;
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>

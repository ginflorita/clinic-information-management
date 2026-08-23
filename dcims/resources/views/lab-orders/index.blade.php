<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">Lab Orders</h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <div class="bg-white shadow-sm rounded p-4">
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('lab-orders.create') }}" class="btn btn-primary">New Lab Order</a>
                </div>

                <table id="data-table" class="table table-striped align-middle w-100">
                    <thead>
                        <tr>
                            <th>Case #</th>
                            <th>Patient</th>
                            <th>Lab</th>
                            <th>Procedure</th>
                            <th>Expected</th>
                            <th>Cost</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($labOrders as $labOrder)
                            @php $transitions = $labOrder->availableTransitions(); @endphp
                            <tr>
                                <td>{{ $labOrder->case_number }}</td>
                                <td><a href="{{ route('patients.show', $labOrder->patient) }}">{{ $labOrder->patient->full_name }}</a></td>
                                <td>{{ $labOrder->lab->name }}</td>
                                <td>{{ $labOrder->procedure->name ?? '—' }}</td>
                                <td>{{ $labOrder->expected_date?->format('Y-m-d') ?? '—' }}</td>
                                <td>{{ $labOrder->cost !== null ? number_format($labOrder->cost, 2) : '—' }}</td>
                                <td>
                                    <span class="badge text-bg-{{ match($labOrder->status) { 'pending' => 'secondary', 'sent' => 'primary', 'in_progress' => 'info', 'ready' => 'warning', 'received' => 'success', 'cancelled' => 'danger', default => 'secondary' } }} text-capitalize">
                                        {{ str_replace('_', ' ', $labOrder->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    @if (! empty($transitions))
                                        <form method="POST" action="{{ route('lab-orders.transition', $labOrder) }}" class="d-flex gap-1 justify-content-end">
                                            @csrf
                                            <select name="status" class="form-select form-select-sm" style="width: auto;">
                                                @foreach ($transitions as $next)
                                                    <option value="{{ $next }}">{{ str_replace('_', ' ', ucfirst($next)) }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-outline-primary">Update</button>
                                        </form>
                                    @endif
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

<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">
            <a href="{{ route('reports.index') }}" class="text-secondary text-decoration-none">Reports</a> / Financial
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4">
            @include('reports._date-range')

            <div class="row row-cols-2 row-cols-md-4 g-3 mb-4">
                <div class="col">
                    <div class="bg-white shadow-sm rounded p-3">
                        <small class="text-secondary d-block">Invoiced</small>
                        <span class="fs-4 fw-semibold">{{ number_format($totalInvoiced, 2) }}</span>
                    </div>
                </div>
                <div class="col">
                    <div class="bg-white shadow-sm rounded p-3">
                        <small class="text-secondary d-block">Payments Received</small>
                        <span class="fs-4 fw-semibold">{{ number_format($totalPayments, 2) }}</span>
                    </div>
                </div>
                <div class="col">
                    <div class="bg-white shadow-sm rounded p-3">
                        <small class="text-secondary d-block">Outstanding Balance</small>
                        <span class="fs-4 fw-semibold">{{ number_format($outstandingBalance, 2) }}</span>
                    </div>
                </div>
                <div class="col">
                    <div class="bg-white shadow-sm rounded p-3">
                        <small class="text-secondary d-block">Discounts / Write-offs</small>
                        <span class="fs-4 fw-semibold">{{ number_format($adjustmentsByType->sum(), 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="bg-white shadow-sm rounded p-4">
                        <h3 class="fs-6 fw-medium mb-3">Revenue by Provider</h3>
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Provider</th>
                                    <th class="text-end">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($revenueByProvider as $row)
                                    <tr>
                                        <td>{{ $row->provider->full_name ?? '—' }}</td>
                                        <td class="text-end">{{ number_format($row->revenue, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="text-secondary">No revenue in this range.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="bg-white shadow-sm rounded p-4">
                        <h3 class="fs-6 fw-medium mb-3">Revenue by Procedure</h3>
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Procedure</th>
                                    <th class="text-end">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($revenueByProcedure as $row)
                                    <tr>
                                        <td>{{ $row->procedure->name ?? '—' }}</td>
                                        <td class="text-end">{{ number_format($row->revenue, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="text-secondary">No revenue in this range.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded p-4 mt-3">
                <h3 class="fs-6 fw-medium mb-3">Adjustments by Type</h3>
                <table class="table table-sm mb-0">
                    <tbody>
                        @forelse ($adjustmentsByType as $type => $total)
                            <tr>
                                <td class="text-capitalize">{{ str_replace('_', ' ', $type) }}</td>
                                <td class="text-end">{{ number_format($total, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td class="text-secondary">No adjustments in this range.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

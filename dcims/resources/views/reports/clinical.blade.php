<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">
            <a href="{{ route('reports.index') }}" class="text-secondary text-decoration-none">Reports</a> / Clinical
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4">
            @include('reports._date-range')

            <div class="row row-cols-2 row-cols-md-4 g-3 mb-4">
                <div class="col">
                    <div class="bg-white shadow-sm rounded p-3">
                        <small class="text-secondary d-block">Procedures Performed</small>
                        <span class="fs-4 fw-semibold">{{ $procedureCount }}</span>
                    </div>
                </div>
                <div class="col">
                    <div class="bg-white shadow-sm rounded p-3">
                        <small class="text-secondary d-block">Procedure Revenue</small>
                        <span class="fs-4 fw-semibold">{{ number_format($procedureRevenue, 2) }}</span>
                    </div>
                </div>
                <div class="col">
                    <div class="bg-white shadow-sm rounded p-3">
                        <small class="text-secondary d-block">Treatment Plans Accepted</small>
                        <span class="fs-4 fw-semibold">{{ ($treatmentPlanCounts['accepted'] ?? 0) + ($treatmentPlanCounts['partially_accepted'] ?? 0) }}</span>
                    </div>
                </div>
                <div class="col">
                    <div class="bg-white shadow-sm rounded p-3">
                        <small class="text-secondary d-block">Treatment Plans Declined</small>
                        <span class="fs-4 fw-semibold">{{ $treatmentPlanCounts['declined'] ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="bg-white shadow-sm rounded p-4">
                        <h3 class="fs-6 fw-medium mb-3">Procedures Performed</h3>
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Procedure</th>
                                    <th class="text-end">Count</th>
                                    <th class="text-end">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($byProcedure as $row)
                                    <tr>
                                        <td>{{ $row->procedure->name ?? '—' }}</td>
                                        <td class="text-end">{{ $row->total }}</td>
                                        <td class="text-end">{{ number_format($row->revenue, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-secondary">No procedures in this range.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="bg-white shadow-sm rounded p-4">
                        <h3 class="fs-6 fw-medium mb-3">Diagnoses Recorded</h3>
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Diagnosis</th>
                                    <th class="text-end">Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($diagnosisCounts as $row)
                                    <tr>
                                        <td>{{ $row->diagnosis->name ?? '—' }}</td>
                                        <td class="text-end">{{ $row->total }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="text-secondary">No diagnoses in this range.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

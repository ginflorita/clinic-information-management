<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">
            <a href="{{ route('reports.index') }}" class="text-secondary text-decoration-none">Reports</a> / Patients
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4">
            <div class="row row-cols-2 row-cols-md-4 g-3 mb-4">
                <div class="col">
                    <div class="bg-white shadow-sm rounded p-3">
                        <small class="text-secondary d-block">Total Patients</small>
                        <span class="fs-4 fw-semibold">{{ $totalPatients }}</span>
                    </div>
                </div>
                <div class="col">
                    <div class="bg-white shadow-sm rounded p-3">
                        <small class="text-secondary d-block">Active</small>
                        <span class="fs-4 fw-semibold">{{ $statusCounts['active'] ?? 0 }}</span>
                    </div>
                </div>
                <div class="col">
                    <div class="bg-white shadow-sm rounded p-3">
                        <small class="text-secondary d-block">Archived</small>
                        <span class="fs-4 fw-semibold">{{ $statusCounts['archived'] ?? 0 }}</span>
                    </div>
                </div>
                <div class="col">
                    <div class="bg-white shadow-sm rounded p-3">
                        <small class="text-secondary d-block">New This Month</small>
                        <span class="fs-4 fw-semibold">{{ $newThisMonth }}</span>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="bg-white shadow-sm rounded p-4">
                        <h3 class="fs-6 fw-medium mb-3">By Sex</h3>
                        <table class="table table-sm mb-0">
                            <tbody>
                                @forelse ($sexCounts as $sex => $count)
                                    <tr>
                                        <td class="text-capitalize">{{ $sex ?: 'Unspecified' }}</td>
                                        <td class="text-end">{{ $count }}</td>
                                    </tr>
                                @empty
                                    <tr><td class="text-secondary">No data.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="bg-white shadow-sm rounded p-4">
                        <h3 class="fs-6 fw-medium mb-3">By Referral Source</h3>
                        <table class="table table-sm mb-0">
                            <tbody>
                                @forelse ($referralSourceCounts as $source => $count)
                                    <tr>
                                        <td class="text-capitalize">{{ $source }}</td>
                                        <td class="text-end">{{ $count }}</td>
                                    </tr>
                                @empty
                                    <tr><td class="text-secondary">No data.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

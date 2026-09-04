<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">
            <a href="{{ route('reports.index') }}" class="text-secondary text-decoration-none">Reports</a> / Appointments
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4">
            @include('reports._date-range')

            <div class="row row-cols-2 row-cols-md-5 g-3 mb-4">
                <div class="col">
                    <div class="bg-white shadow-sm rounded p-3">
                        <small class="text-secondary d-block">Total</small>
                        <span class="fs-4 fw-semibold">{{ $total }}</span>
                    </div>
                </div>
                @foreach (['scheduled', 'rescheduled', 'cancelled', 'no_show'] as $status)
                    <div class="col">
                        <div class="bg-white shadow-sm rounded p-3">
                            <small class="text-secondary d-block text-capitalize">{{ str_replace('_', ' ', $status) }}</small>
                            <span class="fs-4 fw-semibold">{{ $statusCounts[$status] ?? 0 }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="bg-white shadow-sm rounded p-4">
                <h3 class="fs-6 fw-medium mb-3">By Provider</h3>
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Provider</th>
                            <th class="text-end">Appointments</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($byProvider as $row)
                            <tr>
                                <td>{{ $row->provider->full_name ?? '—' }}</td>
                                <td class="text-end">{{ $row->total }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-secondary">No appointments in this range.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

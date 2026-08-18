<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">Treatment Plans</h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded p-4">
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('treatment-plans.create') }}" class="btn btn-primary">New Treatment Plan</a>
                </div>

                <table id="data-table" class="table table-striped align-middle w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Patient</th>
                            <th>Provider</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($treatmentPlans as $plan)
                            <tr>
                                <td>{{ $plan->plan_number }}</td>
                                <td>{{ $plan->patient->full_name }}</td>
                                <td>{{ $plan->provider->full_name }}</td>
                                <td>{{ $plan->title }}</td>
                                <td>
                                    <span class="badge text-bg-{{ match($plan->status) { 'draft' => 'secondary', 'presented' => 'primary', 'accepted', 'partially_accepted' => 'info', 'completed' => 'success', 'declined', 'cancelled', 'expired' => 'danger', default => 'secondary' } }} text-capitalize">
                                        {{ str_replace('_', ' ', $plan->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('treatment-plans.show', $plan) }}" class="btn btn-sm btn-outline-secondary">View</a>
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

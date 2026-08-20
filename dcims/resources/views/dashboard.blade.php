<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4">
            <div class="row row-cols-2 row-cols-md-4 g-3 mb-4">
                <div class="col">
                    <div class="bg-white shadow-sm rounded p-3">
                        <small class="text-secondary d-block">Today's Appointments</small>
                        <span class="fs-4 fw-semibold">{{ $metrics['todays_appointments'] }}</span>
                    </div>
                </div>
                <div class="col">
                    <div class="bg-white shadow-sm rounded p-3">
                        <small class="text-secondary d-block">Today's Patients</small>
                        <span class="fs-4 fw-semibold">{{ $metrics['todays_patients'] }}</span>
                    </div>
                </div>
                <div class="col">
                    <div class="bg-white shadow-sm rounded p-3">
                        <small class="text-secondary d-block">Waiting Patients</small>
                        <span class="fs-4 fw-semibold">{{ $metrics['waiting_patients'] }}</span>
                    </div>
                </div>
                <div class="col">
                    <div class="bg-white shadow-sm rounded p-3">
                        <small class="text-secondary d-block">Currently Treating</small>
                        <span class="fs-4 fw-semibold">{{ $metrics['currently_treating'] }}</span>
                    </div>
                </div>
                <div class="col">
                    <div class="bg-white shadow-sm rounded p-3">
                        <small class="text-secondary d-block">Completed Appointments</small>
                        <span class="fs-4 fw-semibold">{{ $metrics['completed_appointments'] }}</span>
                    </div>
                </div>
                <div class="col">
                    <div class="bg-white shadow-sm rounded p-3">
                        <small class="text-secondary d-block">Cancelled Appointments</small>
                        <span class="fs-4 fw-semibold">{{ $metrics['cancelled_appointments'] }}</span>
                    </div>
                </div>
                <div class="col">
                    <div class="bg-white shadow-sm rounded p-3">
                        <small class="text-secondary d-block">No-show Appointments</small>
                        <span class="fs-4 fw-semibold">{{ $metrics['no_show_appointments'] }}</span>
                    </div>
                </div>
                <div class="col">
                    <div class="bg-white shadow-sm rounded p-3">
                        <small class="text-secondary d-block">New Patients Today</small>
                        <span class="fs-4 fw-semibold">{{ $metrics['new_patients'] }}</span>
                    </div>
                </div>
                <div class="col">
                    <div class="bg-white shadow-sm rounded p-3">
                        <small class="text-secondary d-block">Today's Revenue</small>
                        <span class="fs-4 fw-semibold">{{ number_format($metrics['todays_revenue'], 2) }}</span>
                    </div>
                </div>
                <div class="col">
                    <div class="bg-white shadow-sm rounded p-3">
                        <small class="text-secondary d-block">Outstanding Balances</small>
                        <span class="fs-4 fw-semibold {{ $metrics['outstanding_balances'] > 0 ? 'text-danger' : '' }}">{{ number_format($metrics['outstanding_balances'], 2) }}</span>
                    </div>
                </div>
                <div class="col">
                    <div class="bg-white shadow-sm rounded p-3">
                        <small class="text-secondary d-block">Pending Treatment Plans</small>
                        <span class="fs-4 fw-semibold">{{ $metrics['pending_treatment_plans'] }}</span>
                    </div>
                </div>
                <div class="col">
                    <div class="bg-white shadow-sm rounded p-3">
                        <small class="text-secondary d-block">Follow-up Patients Today</small>
                        <span class="fs-4 fw-semibold">{{ $metrics['follow_up_patients'] }}</span>
                    </div>
                </div>
                <div class="col">
                    <div class="bg-white shadow-sm rounded p-3">
                        <small class="text-secondary d-block">Low-stock Items</small>
                        <span class="fs-4 fw-semibold text-secondary">{{ $metrics['low_stock_items'] }}</span>
                    </div>
                </div>
                <div class="col">
                    <div class="bg-white shadow-sm rounded p-3">
                        <small class="text-secondary d-block">Expiring Inventory</small>
                        <span class="fs-4 fw-semibold text-secondary">{{ $metrics['expiring_inventory'] }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded p-4">
                <h3 class="fs-5 fw-medium mb-3">Recent Activity</h3>
                @if ($recentActivities->isEmpty())
                    <p class="text-secondary mb-0">No recent activity.</p>
                @else
                    <ul class="list-unstyled mb-0">
                        @foreach ($recentActivities as $activity)
                            <li class="d-flex justify-content-between border-bottom py-2">
                                <span>{{ $activity['description'] }}</span>
                                <small class="text-secondary text-nowrap ms-3">{{ $activity['datetime']->diffForHumans() }}</small>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

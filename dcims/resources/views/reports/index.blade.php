<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">Reports</h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4">
            <div class="row row-cols-1 row-cols-md-3 g-3">
                <div class="col">
                    <a href="{{ route('reports.patients') }}" class="text-decoration-none">
                        <div class="bg-white shadow-sm rounded p-4 h-100">
                            <h3 class="fs-5 fw-medium mb-1 text-dark">Patients</h3>
                            <p class="text-secondary small mb-0">Registrations, status breakdown, demographics.</p>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="{{ route('reports.appointments') }}" class="text-decoration-none">
                        <div class="bg-white shadow-sm rounded p-4 h-100">
                            <h3 class="fs-5 fw-medium mb-1 text-dark">Appointments</h3>
                            <p class="text-secondary small mb-0">Volume by status and provider over a date range.</p>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="{{ route('reports.clinical') }}" class="text-decoration-none">
                        <div class="bg-white shadow-sm rounded p-4 h-100">
                            <h3 class="fs-5 fw-medium mb-1 text-dark">Clinical</h3>
                            <p class="text-secondary small mb-0">Procedures performed, treatment plan outcomes, diagnoses.</p>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="{{ route('reports.financial') }}" class="text-decoration-none">
                        <div class="bg-white shadow-sm rounded p-4 h-100">
                            <h3 class="fs-5 fw-medium mb-1 text-dark">Financial</h3>
                            <p class="text-secondary small mb-0">Revenue, payments, outstanding balances, adjustments.</p>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="{{ route('reports.inventory') }}" class="text-decoration-none">
                        <div class="bg-white shadow-sm rounded p-4 h-100">
                            <h3 class="fs-5 fw-medium mb-1 text-dark">Inventory</h3>
                            <p class="text-secondary small mb-0">Stock levels, low stock, expiring batches, purchases.</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

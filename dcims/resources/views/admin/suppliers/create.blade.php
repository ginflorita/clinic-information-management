<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">New Supplier</h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4" style="max-width: 40rem;">
            <div class="bg-white shadow-sm rounded p-4">
                <form method="POST" action="{{ route('admin.suppliers.store') }}">
                    @include('admin.suppliers._form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

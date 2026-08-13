<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">Edit Patient — {{ $patient->full_name }}</h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4" style="max-width: 50rem;">
            <div class="bg-white shadow-sm rounded p-4">
                <form method="POST" action="{{ route('patients.update', $patient) }}">
                    @method('PUT')
                    @include('patients._form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

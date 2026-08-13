<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4 d-flex flex-column gap-4" style="max-width: 40rem;">
            <div class="p-4 bg-white shadow-sm rounded">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="p-4 bg-white shadow-sm rounded">
                @include('profile.partials.update-password-form')
            </div>

            <div class="p-4 bg-white shadow-sm rounded">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>

@csrf

<div class="row">
    <div class="col-md-6 mb-3">
        <x-input-label for="first_name" value="First Name" />
        <x-text-input id="first_name" name="first_name" type="text" :value="old('first_name', $provider->first_name ?? '')" required autofocus />
        <x-input-error :messages="$errors->get('first_name')" class="mt-1" />
    </div>
    <div class="col-md-6 mb-3">
        <x-input-label for="last_name" value="Last Name" />
        <x-text-input id="last_name" name="last_name" type="text" :value="old('last_name', $provider->last_name ?? '')" required />
        <x-input-error :messages="$errors->get('last_name')" class="mt-1" />
    </div>
</div>

<div class="mb-3">
    <x-input-label for="role" value="Role" />
    <select id="role" name="role" class="form-select select2" required>
        @foreach (['dentist' => 'Dentist', 'hygienist' => 'Hygienist', 'assistant' => 'Assistant'] as $value => $label)
            <option value="{{ $value }}" @selected(old('role', $provider->role ?? 'dentist') === $value)>{{ $label }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('role')" class="mt-1" />
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <x-input-label for="license_number" value="License Number" />
        <x-text-input id="license_number" name="license_number" type="text" :value="old('license_number', $provider->license_number ?? '')" />
        <x-input-error :messages="$errors->get('license_number')" class="mt-1" />
    </div>
    <div class="col-md-6 mb-3">
        <x-input-label for="specialization" value="Specialization" />
        <x-text-input id="specialization" name="specialization" type="text" :value="old('specialization', $provider->specialization ?? '')" />
        <x-input-error :messages="$errors->get('specialization')" class="mt-1" />
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <x-input-label for="email" value="Email" />
        <x-text-input id="email" name="email" type="email" :value="old('email', $provider->email ?? '')" />
        <x-input-error :messages="$errors->get('email')" class="mt-1" />
    </div>
    <div class="col-md-6 mb-3">
        <x-input-label for="phone" value="Phone" />
        <x-text-input id="phone" name="phone" type="text" :value="old('phone', $provider->phone ?? '')" />
        <x-input-error :messages="$errors->get('phone')" class="mt-1" />
    </div>
</div>

<div class="mb-3 form-check">
    <input id="is_active" type="checkbox" name="is_active" value="1" class="form-check-input" @checked(old('is_active', $provider->is_active ?? true))>
    <label class="form-check-label" for="is_active">Active</label>
</div>

<div class="d-flex gap-2">
    <x-primary-button>Save</x-primary-button>
    <a href="{{ route('admin.providers.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('.select2').select2({ width: '100%' });
        });
    </script>
@endpush

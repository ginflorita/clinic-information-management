@csrf

<div class="mb-3">
    <x-input-label for="name" value="Name" />
    <x-text-input id="name" name="name" type="text" :value="old('name', $appointmentType->name ?? '')" required autofocus />
    <x-input-error :messages="$errors->get('name')" class="mt-1" />
</div>

<div class="mb-3">
    <x-input-label for="default_duration_minutes" value="Default Duration (minutes)" />
    <x-text-input id="default_duration_minutes" name="default_duration_minutes" type="number" min="5" max="480" :value="old('default_duration_minutes', $appointmentType->default_duration_minutes ?? 30)" required />
    <x-input-error :messages="$errors->get('default_duration_minutes')" class="mt-1" />
</div>

<div class="mb-3">
    <x-input-label for="color" value="Color" />
    <input id="color" name="color" type="color" class="form-control form-control-color" value="{{ old('color', $appointmentType->color ?? '#0d6efd') }}">
    <x-input-error :messages="$errors->get('color')" class="mt-1" />
</div>

<div class="mb-3 form-check">
    <input id="is_active" type="checkbox" name="is_active" value="1" class="form-check-input" @checked(old('is_active', $appointmentType->is_active ?? true))>
    <label class="form-check-label" for="is_active">Active</label>
</div>

<div class="d-flex gap-2">
    <x-primary-button>Save</x-primary-button>
    <a href="{{ route('admin.appointment-types.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>

@csrf

<div class="mb-3">
    <x-input-label for="name" value="Name" />
    <x-text-input id="name" name="name" type="text" :value="old('name', $chair->name ?? '')" required autofocus />
    <x-input-error :messages="$errors->get('name')" class="mt-1" />
</div>

<div class="mb-3">
    <x-input-label for="location" value="Location" />
    <x-text-input id="location" name="location" type="text" :value="old('location', $chair->location ?? '')" />
    <x-input-error :messages="$errors->get('location')" class="mt-1" />
</div>

<div class="mb-3 form-check">
    <input id="is_active" type="checkbox" name="is_active" value="1" class="form-check-input" @checked(old('is_active', $chair->is_active ?? true))>
    <label class="form-check-label" for="is_active">Active</label>
</div>

<div class="d-flex gap-2">
    <x-primary-button>Save</x-primary-button>
    <a href="{{ route('admin.chairs.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>

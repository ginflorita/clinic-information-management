@csrf

<div class="mb-3">
    <x-input-label for="name" value="Name" />
    <x-text-input id="name" name="name" type="text" :value="old('name', $consentType->name ?? '')" required autofocus placeholder="e.g. Extraction Consent" />
    <x-input-error :messages="$errors->get('name')" class="mt-1" />
</div>

<div class="mb-3">
    <x-input-label for="description" value="Description" />
    <textarea id="description" name="description" class="form-control" rows="3">{{ old('description', $consentType->description ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-1" />
</div>

<div class="mb-3 form-check">
    <input id="is_active" type="checkbox" name="is_active" value="1" class="form-check-input" @checked(old('is_active', $consentType->is_active ?? true))>
    <label class="form-check-label" for="is_active">Active</label>
</div>

<div class="d-flex gap-2">
    <x-primary-button>Save</x-primary-button>
    <a href="{{ route('admin.consent-types.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>

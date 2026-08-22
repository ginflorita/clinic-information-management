@csrf

<div class="mb-3">
    <x-input-label for="generic_name" value="Generic Name" />
    <x-text-input id="generic_name" name="generic_name" type="text" :value="old('generic_name', $medication->generic_name ?? '')" required autofocus />
    <x-input-error :messages="$errors->get('generic_name')" class="mt-1" />
</div>

<div class="mb-3">
    <x-input-label for="brand_name" value="Brand Name" />
    <x-text-input id="brand_name" name="brand_name" type="text" :value="old('brand_name', $medication->brand_name ?? '')" />
    <x-input-error :messages="$errors->get('brand_name')" class="mt-1" />
</div>

<div class="mb-3">
    <x-input-label for="dosage_form" value="Dosage Form" />
    <x-text-input id="dosage_form" name="dosage_form" type="text" :value="old('dosage_form', $medication->dosage_form ?? '')" placeholder="e.g. tablet, capsule, syrup" />
    <x-input-error :messages="$errors->get('dosage_form')" class="mt-1" />
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <x-input-label for="strength" value="Strength" />
        <x-text-input id="strength" name="strength" type="text" :value="old('strength', $medication->strength ?? '')" placeholder="e.g. 500" />
        <x-input-error :messages="$errors->get('strength')" class="mt-1" />
    </div>
    <div class="col-md-6 mb-3">
        <x-input-label for="unit" value="Unit" />
        <x-text-input id="unit" name="unit" type="text" :value="old('unit', $medication->unit ?? '')" placeholder="e.g. mg" />
        <x-input-error :messages="$errors->get('unit')" class="mt-1" />
    </div>
</div>

<div class="mb-3 form-check">
    <input id="is_active" type="checkbox" name="is_active" value="1" class="form-check-input" @checked(old('is_active', $medication->is_active ?? true))>
    <label class="form-check-label" for="is_active">Active</label>
</div>

<div class="d-flex gap-2">
    <x-primary-button>Save</x-primary-button>
    <a href="{{ route('admin.medications.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>

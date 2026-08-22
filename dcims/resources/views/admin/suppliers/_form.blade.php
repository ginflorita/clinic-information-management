@csrf

<div class="mb-3">
    <x-input-label for="name" value="Name" />
    <x-text-input id="name" name="name" type="text" :value="old('name', $supplier->name ?? '')" required autofocus />
    <x-input-error :messages="$errors->get('name')" class="mt-1" />
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <x-input-label for="contact_person" value="Contact Person" />
        <x-text-input id="contact_person" name="contact_person" type="text" :value="old('contact_person', $supplier->contact_person ?? '')" />
        <x-input-error :messages="$errors->get('contact_person')" class="mt-1" />
    </div>
    <div class="col-md-6 mb-3">
        <x-input-label for="phone" value="Phone" />
        <x-text-input id="phone" name="phone" type="text" :value="old('phone', $supplier->phone ?? '')" />
        <x-input-error :messages="$errors->get('phone')" class="mt-1" />
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <x-input-label for="email" value="Email" />
        <x-text-input id="email" name="email" type="email" :value="old('email', $supplier->email ?? '')" />
        <x-input-error :messages="$errors->get('email')" class="mt-1" />
    </div>
    <div class="col-md-6 mb-3">
        <x-input-label for="tax_information" value="Tax Information" />
        <x-text-input id="tax_information" name="tax_information" type="text" :value="old('tax_information', $supplier->tax_information ?? '')" />
        <x-input-error :messages="$errors->get('tax_information')" class="mt-1" />
    </div>
</div>

<div class="mb-3">
    <x-input-label for="address" value="Address" />
    <textarea id="address" name="address" class="form-control" rows="2">{{ old('address', $supplier->address ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('address')" class="mt-1" />
</div>

<div class="mb-3 form-check">
    <input id="is_active" type="checkbox" name="is_active" value="1" class="form-check-input" @checked(old('is_active', $supplier->is_active ?? true))>
    <label class="form-check-label" for="is_active">Active</label>
</div>

<div class="d-flex gap-2">
    <x-primary-button>Save</x-primary-button>
    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>

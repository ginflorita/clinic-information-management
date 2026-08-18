@csrf

<div class="mb-3">
    <x-input-label for="code" value="Code" />
    <x-text-input id="code" name="code" type="text" :value="old('code', $diagnosis->code ?? '')" required autofocus />
    <x-input-error :messages="$errors->get('code')" class="mt-1" />
</div>

<div class="mb-3">
    <x-input-label for="name" value="Name" />
    <x-text-input id="name" name="name" type="text" :value="old('name', $diagnosis->name ?? '')" required />
    <x-input-error :messages="$errors->get('name')" class="mt-1" />
</div>

<div class="mb-3">
    <x-input-label for="category" value="Category" />
    <x-text-input id="category" name="category" type="text" :value="old('category', $diagnosis->category ?? '')" />
    <x-input-error :messages="$errors->get('category')" class="mt-1" />
</div>

<div class="mb-3">
    <x-input-label for="description" value="Description" />
    <textarea id="description" name="description" class="form-control" rows="3">{{ old('description', $diagnosis->description ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-1" />
</div>

<div class="mb-3 form-check">
    <input id="is_active" type="checkbox" name="is_active" value="1" class="form-check-input" @checked(old('is_active', $diagnosis->is_active ?? true))>
    <label class="form-check-label" for="is_active">Active</label>
</div>

<div class="d-flex gap-2">
    <x-primary-button>Save</x-primary-button>
    <a href="{{ route('admin.diagnoses.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>

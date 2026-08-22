@csrf

<div class="mb-3">
    <x-input-label for="name" value="Name" />
    <x-text-input id="name" name="name" type="text" :value="old('name', $role->name ?? '')" required autofocus placeholder="e.g. Receptionist" />
    <x-input-error :messages="$errors->get('name')" class="mt-1" />
</div>

<div class="mb-3">
    <x-input-label for="description" value="Description" />
    <textarea id="description" name="description" class="form-control" rows="2">{{ old('description', $role->description ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-1" />
</div>

<div class="mb-3">
    <x-input-label value="Module Access" />
    <p class="small text-secondary mb-2">Users with this role can only see and use the modules checked below.</p>
    <div class="row g-2">
        @foreach ($modules as $key => $label)
            <div class="col-md-4 form-check">
                <input id="module-{{ $key }}" type="checkbox" name="modules[]" value="{{ $key }}" class="form-check-input"
                    @checked(collect(old('modules', $grantedModules ?? []))->contains($key))>
                <label class="form-check-label" for="module-{{ $key }}">{{ $label }}</label>
            </div>
        @endforeach
    </div>
    <x-input-error :messages="$errors->get('modules')" class="mt-1" />
</div>

<div class="d-flex gap-2">
    <x-primary-button>Save</x-primary-button>
    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>

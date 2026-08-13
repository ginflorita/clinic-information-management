@csrf

<div class="mb-3">
    <x-input-label for="code" value="Code" />
    <x-text-input id="code" name="code" type="text" :value="old('code', $procedure->code ?? '')" required autofocus />
    <x-input-error :messages="$errors->get('code')" class="mt-1" />
</div>

<div class="mb-3">
    <x-input-label for="name" value="Name" />
    <x-text-input id="name" name="name" type="text" :value="old('name', $procedure->name ?? '')" required />
    <x-input-error :messages="$errors->get('name')" class="mt-1" />
</div>

<div class="mb-3">
    <x-input-label for="procedure_category_id" value="Category" />
    <select id="procedure_category_id" name="procedure_category_id" class="form-select select2">
        <option value=""></option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}" @selected(old('procedure_category_id', $procedure->procedure_category_id ?? null) == $category->id)>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('procedure_category_id')" class="mt-1" />
</div>

<div class="mb-3">
    <x-input-label for="description" value="Description" />
    <textarea id="description" name="description" class="form-control" rows="3">{{ old('description', $procedure->description ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-1" />
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <x-input-label for="default_fee" value="Default Fee" />
        <x-text-input id="default_fee" name="default_fee" type="number" step="0.01" min="0" :value="old('default_fee', $procedure->default_fee ?? 0)" required />
        <x-input-error :messages="$errors->get('default_fee')" class="mt-1" />
    </div>
    <div class="col-md-6 mb-3">
        <x-input-label for="default_duration_minutes" value="Default Duration (minutes)" />
        <x-text-input id="default_duration_minutes" name="default_duration_minutes" type="number" min="5" max="480" :value="old('default_duration_minutes', $procedure->default_duration_minutes ?? 30)" required />
        <x-input-error :messages="$errors->get('default_duration_minutes')" class="mt-1" />
    </div>
</div>

<div class="mb-3 form-check">
    <input id="is_active" type="checkbox" name="is_active" value="1" class="form-check-input" @checked(old('is_active', $procedure->is_active ?? true))>
    <label class="form-check-label" for="is_active">Active</label>
</div>

<div class="d-flex gap-2">
    <x-primary-button>Save</x-primary-button>
    <a href="{{ route('admin.procedures.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('.select2').select2({ width: '100%', allowClear: true, placeholder: 'Select a category' });
        });
    </script>
@endpush

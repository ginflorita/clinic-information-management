@csrf

<div class="row">
    <div class="col-md-6 mb-3">
        <x-input-label for="sku" value="SKU" />
        <x-text-input id="sku" name="sku" type="text" :value="old('sku', $product->sku ?? '')" required autofocus />
        <x-input-error :messages="$errors->get('sku')" class="mt-1" />
    </div>
    <div class="col-md-6 mb-3">
        <x-input-label for="name" value="Name" />
        <x-text-input id="name" name="name" type="text" :value="old('name', $product->name ?? '')" required />
        <x-input-error :messages="$errors->get('name')" class="mt-1" />
    </div>
</div>

<div class="mb-3">
    <x-input-label for="description" value="Description" />
    <textarea id="description" name="description" class="form-control" rows="2">{{ old('description', $product->description ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-1" />
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <x-input-label for="category_id" value="Category" />
        <select id="category_id" name="category_id" class="form-select select2">
            <option value="">None</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id ?? '') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('category_id')" class="mt-1" />
    </div>
    <div class="col-md-6 mb-3">
        <x-input-label for="unit_id" value="Unit" />
        <select id="unit_id" name="unit_id" class="form-select select2">
            <option value="">None</option>
            @foreach ($units as $unit)
                <option value="{{ $unit->id }}" @selected(old('unit_id', $product->unit_id ?? '') == $unit->id)>{{ $unit->name }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('unit_id')" class="mt-1" />
    </div>
</div>

<div class="mb-3" style="max-width: 12rem;">
    <x-input-label for="reorder_level" value="Reorder Level" />
    <x-text-input id="reorder_level" name="reorder_level" type="number" step="0.01" min="0" :value="old('reorder_level', $product->reorder_level ?? 0)" required />
    <small class="text-secondary d-block mt-1">Flagged as low stock once quantity on hand drops below this.</small>
    <x-input-error :messages="$errors->get('reorder_level')" class="mt-1" />
</div>

<div class="mb-3 form-check">
    <input id="is_active" type="checkbox" name="is_active" value="1" class="form-check-input" @checked(old('is_active', $product->is_active ?? true))>
    <label class="form-check-label" for="is_active">Active</label>
</div>

<div class="d-flex gap-2">
    <x-primary-button>Save</x-primary-button>
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('.select2').select2({ width: '100%' });
        });
    </script>
@endpush

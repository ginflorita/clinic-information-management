<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">New Purchase Order</h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4" style="max-width: 40rem;">
            <div class="bg-white shadow-sm rounded p-4">
                <form method="POST" action="{{ route('purchase-orders.store') }}">
                    @csrf

                    <div class="mb-3">
                        <x-input-label for="supplier_id" value="Supplier" />
                        <select id="supplier_id" name="supplier_id" class="form-select select2" required>
                            <option value=""></option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('supplier_id')" class="mt-1" />
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <x-input-label for="order_date" value="Order Date" />
                            <x-text-input id="order_date" name="order_date" type="date" :value="old('order_date', now()->toDateString())" required />
                            <x-input-error :messages="$errors->get('order_date')" class="mt-1" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <x-input-label for="expected_date" value="Expected Date" />
                            <x-text-input id="expected_date" name="expected_date" type="date" :value="old('expected_date')" />
                            <x-input-error :messages="$errors->get('expected_date')" class="mt-1" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <x-input-label for="notes" value="Notes" />
                        <textarea id="notes" name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <x-primary-button>Create Purchase Order</x-primary-button>
                        <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                $('.select2').select2({ width: '100%' });
            });
        </script>
    @endpush
</x-app-layout>

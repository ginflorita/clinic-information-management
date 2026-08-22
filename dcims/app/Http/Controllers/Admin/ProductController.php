<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryCategory;
use App\Models\InventoryUnit;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        return view('admin.products.index', [
            'products' => Product::with(['category', 'unit'])->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.products.create', $this->formOptions());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateProduct($request);
        $data['is_active'] = $request->boolean('is_active');

        Product::create($data);

        return redirect()->route('admin.products.index')->with('status', 'Product created.');
    }

    public function edit(Product $product): View
    {
        return view('admin.products.edit', $this->formOptions() + ['product' => $product]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validateProduct($request, $product);
        $data['is_active'] = $request->boolean('is_active');

        $product->update($data);

        return redirect()->route('admin.products.index')->with('status', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('admin.products.index')->with('status', 'Product deleted.');
    }

    private function formOptions(): array
    {
        return [
            'categories' => InventoryCategory::where('is_active', true)->orderBy('name')->get(),
            'units' => InventoryUnit::where('is_active', true)->orderBy('name')->get(),
        ];
    }

    private function validateProduct(Request $request, ?Product $product = null): array
    {
        return $request->validate([
            'sku' => ['required', 'string', 'max:255', Rule::unique('products', 'sku')->ignore($product?->id)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:inventory_categories,id'],
            'unit_id' => ['nullable', 'exists:inventory_units,id'],
            'reorder_level' => ['required', 'numeric', 'min:0'],
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryCategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.inventory-categories.index', [
            'inventoryCategories' => InventoryCategory::orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.inventory-categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        InventoryCategory::create($data);

        return redirect()->route('admin.inventory-categories.index')->with('status', 'Inventory category created.');
    }

    public function edit(InventoryCategory $inventoryCategory): View
    {
        return view('admin.inventory-categories.edit', ['inventoryCategory' => $inventoryCategory]);
    }

    public function update(Request $request, InventoryCategory $inventoryCategory): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        $inventoryCategory->update($data);

        return redirect()->route('admin.inventory-categories.index')->with('status', 'Inventory category updated.');
    }

    public function destroy(InventoryCategory $inventoryCategory): RedirectResponse
    {
        $inventoryCategory->delete();

        return redirect()->route('admin.inventory-categories.index')->with('status', 'Inventory category deleted.');
    }
}

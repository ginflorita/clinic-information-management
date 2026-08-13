<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryUnit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryUnitController extends Controller
{
    public function index(): View
    {
        return view('admin.inventory-units.index', [
            'inventoryUnits' => InventoryUnit::orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.inventory-units.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'abbreviation' => ['nullable', 'string', 'max:20'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        InventoryUnit::create($data);

        return redirect()->route('admin.inventory-units.index')->with('status', 'Inventory unit created.');
    }

    public function edit(InventoryUnit $inventoryUnit): View
    {
        return view('admin.inventory-units.edit', ['inventoryUnit' => $inventoryUnit]);
    }

    public function update(Request $request, InventoryUnit $inventoryUnit): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'abbreviation' => ['nullable', 'string', 'max:20'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        $inventoryUnit->update($data);

        return redirect()->route('admin.inventory-units.index')->with('status', 'Inventory unit updated.');
    }

    public function destroy(InventoryUnit $inventoryUnit): RedirectResponse
    {
        $inventoryUnit->delete();

        return redirect()->route('admin.inventory-units.index')->with('status', 'Inventory unit deleted.');
    }
}

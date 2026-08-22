<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(): View
    {
        return view('admin.suppliers.index', [
            'suppliers' => Supplier::orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.suppliers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateSupplier($request);
        $data['is_active'] = $request->boolean('is_active');

        Supplier::create($data);

        return redirect()->route('admin.suppliers.index')->with('status', 'Supplier created.');
    }

    public function edit(Supplier $supplier): View
    {
        return view('admin.suppliers.edit', ['supplier' => $supplier]);
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $data = $this->validateSupplier($request);
        $data['is_active'] = $request->boolean('is_active');

        $supplier->update($data);

        return redirect()->route('admin.suppliers.index')->with('status', 'Supplier updated.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        $supplier->delete();

        return redirect()->route('admin.suppliers.index')->with('status', 'Supplier deleted.');
    }

    private function validateSupplier(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'tax_information' => ['nullable', 'string', 'max:255'],
        ]);
    }
}

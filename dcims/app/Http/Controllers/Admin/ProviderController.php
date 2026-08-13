<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProviderController extends Controller
{
    public function index(): View
    {
        return view('admin.providers.index', [
            'providers' => Provider::orderBy('last_name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.providers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'in:dentist,hygienist,assistant'],
            'license_number' => ['nullable', 'string', 'max:100'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        Provider::create($data);

        return redirect()->route('admin.providers.index')->with('status', 'Provider created.');
    }

    public function edit(Provider $provider): View
    {
        return view('admin.providers.edit', ['provider' => $provider]);
    }

    public function update(Request $request, Provider $provider): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'in:dentist,hygienist,assistant'],
            'license_number' => ['nullable', 'string', 'max:100'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        $provider->update($data);

        return redirect()->route('admin.providers.index')->with('status', 'Provider updated.');
    }

    public function destroy(Provider $provider): RedirectResponse
    {
        $provider->delete();

        return redirect()->route('admin.providers.index')->with('status', 'Provider deleted.');
    }
}

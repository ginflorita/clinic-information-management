<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConsentType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConsentTypeController extends Controller
{
    public function index(): View
    {
        return view('admin.consent-types.index', [
            'consentTypes' => ConsentType::orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.consent-types.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active');

        ConsentType::create($data);

        return redirect()->route('admin.consent-types.index')->with('status', 'Consent type created.');
    }

    public function edit(ConsentType $consentType): View
    {
        return view('admin.consent-types.edit', ['consentType' => $consentType]);
    }

    public function update(Request $request, ConsentType $consentType): RedirectResponse
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active');

        $consentType->update($data);

        return redirect()->route('admin.consent-types.index')->with('status', 'Consent type updated.');
    }

    public function destroy(ConsentType $consentType): RedirectResponse
    {
        $consentType->delete();

        return redirect()->route('admin.consent-types.index')->with('status', 'Consent type deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);
    }
}

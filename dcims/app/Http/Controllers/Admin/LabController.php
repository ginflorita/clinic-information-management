<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lab;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LabController extends Controller
{
    public function index(): View
    {
        return view('admin.labs.index', [
            'labs' => Lab::orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.labs.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active');

        Lab::create($data);

        return redirect()->route('admin.labs.index')->with('status', 'Lab created.');
    }

    public function edit(Lab $lab): View
    {
        return view('admin.labs.edit', ['lab' => $lab]);
    }

    public function update(Request $request, Lab $lab): RedirectResponse
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active');

        $lab->update($data);

        return redirect()->route('admin.labs.index')->with('status', 'Lab updated.');
    }

    public function destroy(Lab $lab): RedirectResponse
    {
        $lab->delete();

        return redirect()->route('admin.labs.index')->with('status', 'Lab deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RecallType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecallTypeController extends Controller
{
    public function index(): View
    {
        return view('admin.recall-types.index', [
            'recallTypes' => RecallType::orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.recall-types.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active');

        RecallType::create($data);

        return redirect()->route('admin.recall-types.index')->with('status', 'Recall type created.');
    }

    public function edit(RecallType $recallType): View
    {
        return view('admin.recall-types.edit', ['recallType' => $recallType]);
    }

    public function update(Request $request, RecallType $recallType): RedirectResponse
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active');

        $recallType->update($data);

        return redirect()->route('admin.recall-types.index')->with('status', 'Recall type updated.');
    }

    public function destroy(RecallType $recallType): RedirectResponse
    {
        $recallType->delete();

        return redirect()->route('admin.recall-types.index')->with('status', 'Recall type deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'default_interval_months' => ['nullable', 'integer', 'min:1'],
        ]);
    }
}

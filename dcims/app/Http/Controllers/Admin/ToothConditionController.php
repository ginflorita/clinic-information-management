<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ToothCondition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ToothConditionController extends Controller
{
    public function index(): View
    {
        return view('admin.tooth-conditions.index', [
            'toothConditions' => ToothCondition::orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.tooth-conditions.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:tooth_conditions,code'],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        ToothCondition::create($data);

        return redirect()->route('admin.tooth-conditions.index')->with('status', 'Tooth condition created.');
    }

    public function edit(ToothCondition $toothCondition): View
    {
        return view('admin.tooth-conditions.edit', ['toothCondition' => $toothCondition]);
    }

    public function update(Request $request, ToothCondition $toothCondition): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:tooth_conditions,code,'.$toothCondition->id],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        $toothCondition->update($data);

        return redirect()->route('admin.tooth-conditions.index')->with('status', 'Tooth condition updated.');
    }

    public function destroy(ToothCondition $toothCondition): RedirectResponse
    {
        $toothCondition->delete();

        return redirect()->route('admin.tooth-conditions.index')->with('status', 'Tooth condition deleted.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProcedureCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProcedureCategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.procedure-categories.index', [
            'procedureCategories' => ProcedureCategory::orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.procedure-categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        ProcedureCategory::create($data);

        return redirect()->route('admin.procedure-categories.index')->with('status', 'Procedure category created.');
    }

    public function edit(ProcedureCategory $procedureCategory): View
    {
        return view('admin.procedure-categories.edit', ['procedureCategory' => $procedureCategory]);
    }

    public function update(Request $request, ProcedureCategory $procedureCategory): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        $procedureCategory->update($data);

        return redirect()->route('admin.procedure-categories.index')->with('status', 'Procedure category updated.');
    }

    public function destroy(ProcedureCategory $procedureCategory): RedirectResponse
    {
        $procedureCategory->delete();

        return redirect()->route('admin.procedure-categories.index')->with('status', 'Procedure category deleted.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Procedure;
use App\Models\ProcedureCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProcedureController extends Controller
{
    public function index(): View
    {
        return view('admin.procedures.index', [
            'procedures' => Procedure::with('category')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.procedures.create', [
            'categories' => ProcedureCategory::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'procedure_category_id' => ['nullable', 'exists:procedure_categories,id'],
            'code' => ['required', 'string', 'max:50', 'unique:procedures,code'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'default_fee' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'default_duration_minutes' => ['required', 'integer', 'min:5', 'max:480'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        Procedure::create($data);

        return redirect()->route('admin.procedures.index')->with('status', 'Procedure created.');
    }

    public function edit(Procedure $procedure): View
    {
        return view('admin.procedures.edit', [
            'procedure' => $procedure,
            'categories' => ProcedureCategory::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Procedure $procedure): RedirectResponse
    {
        $data = $request->validate([
            'procedure_category_id' => ['nullable', 'exists:procedure_categories,id'],
            'code' => ['required', 'string', 'max:50', 'unique:procedures,code,'.$procedure->id],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'default_fee' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'default_duration_minutes' => ['required', 'integer', 'min:5', 'max:480'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        $procedure->update($data);

        return redirect()->route('admin.procedures.index')->with('status', 'Procedure updated.');
    }

    public function destroy(Procedure $procedure): RedirectResponse
    {
        $procedure->delete();

        return redirect()->route('admin.procedures.index')->with('status', 'Procedure deleted.');
    }
}

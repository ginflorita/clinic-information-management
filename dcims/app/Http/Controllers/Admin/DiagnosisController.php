<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Diagnosis;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DiagnosisController extends Controller
{
    public function index(): View
    {
        return view('admin.diagnoses.index', [
            'diagnoses' => Diagnosis::orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.diagnoses.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:diagnoses,code'],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        Diagnosis::create($data);

        return redirect()->route('admin.diagnoses.index')->with('status', 'Diagnosis created.');
    }

    public function edit(Diagnosis $diagnosis): View
    {
        return view('admin.diagnoses.edit', ['diagnosis' => $diagnosis]);
    }

    public function update(Request $request, Diagnosis $diagnosis): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:diagnoses,code,'.$diagnosis->id],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        $diagnosis->update($data);

        return redirect()->route('admin.diagnoses.index')->with('status', 'Diagnosis updated.');
    }

    public function destroy(Diagnosis $diagnosis): RedirectResponse
    {
        $diagnosis->delete();

        return redirect()->route('admin.diagnoses.index')->with('status', 'Diagnosis deleted.');
    }
}

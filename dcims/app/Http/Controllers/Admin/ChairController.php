<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chair;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChairController extends Controller
{
    public function index(): View
    {
        return view('admin.chairs.index', [
            'chairs' => Chair::orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.chairs.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        Chair::create($data);

        return redirect()->route('admin.chairs.index')->with('status', 'Chair created.');
    }

    public function edit(Chair $chair): View
    {
        return view('admin.chairs.edit', ['chair' => $chair]);
    }

    public function update(Request $request, Chair $chair): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        $chair->update($data);

        return redirect()->route('admin.chairs.index')->with('status', 'Chair updated.');
    }

    public function destroy(Chair $chair): RedirectResponse
    {
        $chair->delete();

        return redirect()->route('admin.chairs.index')->with('status', 'Chair deleted.');
    }
}

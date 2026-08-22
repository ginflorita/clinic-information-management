<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        return view('admin.roles.index', [
            'roles' => Role::withCount('users')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.roles.create', [
            'modules' => Role::MODULES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($data) {
            $role = Role::create(['name' => $data['name'], 'description' => $data['description']]);

            foreach ($data['modules'] as $module) {
                $role->permissions()->create(['module' => $module]);
            }
        });

        return redirect()->route('admin.roles.index')->with('status', 'Role created.');
    }

    public function edit(Role $role): View
    {
        return view('admin.roles.edit', [
            'role' => $role,
            'modules' => Role::MODULES,
            'grantedModules' => $role->permissions()->pluck('module')->all(),
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($role, $data) {
            $role->update(['name' => $data['name'], 'description' => $data['description']]);

            $role->permissions()->whereNotIn('module', $data['modules'])->delete();

            $existing = $role->permissions()->pluck('module')->all();
            foreach (array_diff($data['modules'], $existing) as $module) {
                $role->permissions()->create(['module' => $module]);
            }
        });

        return redirect()->route('admin.roles.index')->with('status', 'Role updated.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->users()->exists()) {
            throw ValidationException::withMessages([
                'role' => 'This role is still assigned to one or more users. Reassign them before deleting it.',
            ]);
        }

        $role->delete();

        return redirect()->route('admin.roles.index')->with('status', 'Role deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->ignore($request->route('role'))],
            'description' => ['nullable', 'string'],
            'modules' => ['array'],
            'modules.*' => [Rule::in(array_keys(Role::MODULES))],
        ]);
        $data['description'] = $data['description'] ?? null;
        $data['modules'] = $data['modules'] ?? [];

        return $data;
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\RoleRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RoleController extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->setClass('role');
        $this->table = 'roles';
    }

    public function index(Request $request)
    {
        return view('admin.roles.index', [
            'data' => Role::filter($request)->paginateRequested($request, $this->limit),
            'route_create' => $this->checkPerm($this->table.'.create'),
            'route_edit' => $this->checkPerm($this->table.'.edit'),
        ]);
    }

    public function create()
    {
        return view('admin.roles.create', [
            'permissions' => $this->groupedPermissions(),
            'rolePermissions' => [],
        ]);
    }

    public function store(RoleRequest $request)
    {
        $input = $request->validated();

        $role = Role::create([
            'name' => $input['name'],
            'display_name' => $input['display_name'],
            'description' => $input['description'] ?? null,
        ]);

        $role->syncPermissions($this->permissionsFrom($request));

        return redirect()->route('admin.roles.index')->with('success', __('Role created successfully'));
    }

    public function show(Request $request, $id)
    {
        return $this->edit($id);
    }

    public function edit($id)
    {
        $role = Role::with('permissions')->findOrFail($id);

        return view('admin.roles.edit', [
            'role' => $role,
            'permissions' => $this->groupedPermissions(),
            'rolePermissions' => $role->permissions->pluck('id')->toArray(),
        ]);
    }

    public function update(RoleRequest $request, int $id)
    {
        $role = Role::findOrFail($id);
        Gate::authorize('update', $role);

        $input = $request->validated();

        if ($role->id === 1) {
            $input['name'] = $role->name;
        }

        $role->update([
            'name' => $input['name'],
            'display_name' => $input['display_name'],
            'description' => $input['description'] ?? $role->description,
        ]);

        $role->syncPermissions($this->permissionsFrom($request));

        return redirect()->route('admin.roles.index')->with('success', __('Role updated successfully'));
    }

    public function destroy($id)
    {
        $role = Role::find($id);

        if (! $role || $role->id === 1) {
            return redirect()->route('admin.roles.index')->with('error', __('Role deleted failed'));
        }

        Gate::authorize('delete', $role);

        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', __('Role deleted successfully'));
    }

    private function groupedPermissions(): array
    {
        return Permission::query()
            ->select('id', 'display_name', 'description', 'name')
            ->orderBy('description')
            ->orderBy('id')
            ->get()
            ->groupBy('description')
            ->map(fn ($items, $description) => [
                'category_name' => getModuleTableModelName($description),
                'permissions' => $items->pluck('display_name', 'id')->toArray(),
            ])
            ->values()
            ->toArray();
    }

    private function permissionsFrom(Request $request)
    {
        return Permission::whereIn('id', (array) $request->input('permissions', []))->get();
    }
}

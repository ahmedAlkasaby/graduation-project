<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\RoleFilterRequest;
use App\Http\Requests\Admin\RoleRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Services\User\PermissionGroupService;
use App\Services\User\RoleService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

/**
 * Managing dashboard roles and their permissions from the Blade admin panel,
 * the twin of Api\V1\Admin\RoleController. Roles only matter for the admin
 * user type. The form's permission list comes from the same
 * PermissionGroupService the API's permissions endpoint uses.
 */
class RoleController extends AdminController
{
    public function __construct(
        private readonly RoleService $roleService,
        private readonly PermissionGroupService $permissionGroups,
    ) {
        parent::__construct();
        $this->setClass('role');
    }

    /** The accepted filters are listed in RoleFilterRequest. */
    public function index(RoleFilterRequest $request): View
    {
        $filters = $request->validated();

        $roles = Role::query()
            ->withCount(['users', 'permissions'])
            ->filter($filters)
            ->paginate($this->perPage($filters))
            ->withQueryString();

        return $this->listView('admin.roles.index', $roles, 'roles', [
            'systemRoles' => array_keys(config('laratrust_seeder.roles_structure', [])),
        ]);
    }

    public function create(): View
    {
        return view('admin.roles.create', [
            'permissions' => $this->permissionGroupsForForm(),
            'rolePermissions' => [],
            'isSystemRole' => false,
        ]);
    }

    public function store(RoleRequest $request): RedirectResponse
    {
        $this->roleService->create($request->validated());

        return redirect()->route('admin.roles.index')->with('success', __('Created successfully'));
    }

    public function show(Role $role): View
    {
        $role->load('permissions')->loadCount('users');

        return view('admin.roles.show', [
            'role' => $role,
            'totalPermissions' => Permission::count(),
            'modules' => $role->permissions
                ->groupBy(fn (Permission $permission): string => $permission->description ?? '')
                ->map(fn (Collection $permissions, string $module): array => [
                    'name' => $module === '' ? __('Custom Permissions') : $this->permissionGroups->moduleLabel($module),
                    'permissions' => $permissions->map(fn (Permission $permission): string => $module === ''
                        ? $this->permissionGroups->customLabel($permission)
                        : __(Str::headline($permission->label())))->all(),
                ])
                ->values(),
        ]);
    }

    public function edit(Role $role): View|RedirectResponse
    {
        if (Gate::denies('update', $role)) {
            return redirect()->route('admin.unauthorized');
        }

        return view('admin.roles.edit', [
            'role' => $role,
            'isSystemRole' => $this->roleService->isSystemRole($role),
            'permissions' => $this->permissionGroupsForForm(),
            'rolePermissions' => $role->permissions()->pluck('id')->all(),
        ]);
    }

    public function update(RoleRequest $request, Role $role): RedirectResponse
    {
        if (Gate::denies('update', $role)) {
            return redirect()->route('admin.unauthorized');
        }

        // An unchecked box is not posted, so an empty list means "none".
        $this->roleService->update($role, $request->validated() + ['permissions' => []]);

        return redirect()->route('admin.roles.index')->with('success', __('Updated successfully'));
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($this->roleService->isSystemRole($role)) {
            return redirect()->route('admin.roles.index')->with('error', __('This role cannot be deleted'));
        }

        if ($this->roleService->isAssigned($role)) {
            return redirect()->route('admin.roles.index')->with('error', __('This role is assigned to users'));
        }

        $this->roleService->delete($role);

        return redirect()->route('admin.roles.index')->with('success', __('Deleted successfully'));
    }

    /**
     * The permissions the signed in admin may hand out, shaped for the
     * permissions-table partial.
     *
     * @return array{
     *     resources: list<array{key: string, category_name: string, permissions: array<int, string>}>,
     *     custom: list<array{id: int, name: string, label: string}>
     * }
     */
    private function permissionGroupsForForm(): array
    {
        $grouped = $this->permissionGroups->grouped($this->admin());

        return [
            'resources' => $grouped['resources']
                ->map(fn (Collection $permissions, string $module): array => [
                    'key' => $module,
                    'category_name' => $this->permissionGroups->moduleLabel($module),
                    'permissions' => $permissions->mapWithKeys(fn (Permission $permission): array => [
                        $permission->id => __(Str::headline($permission->label())),
                    ])->all(),
                ])
                ->values()
                ->all(),
            'custom' => $grouped['custom']
                ->map(fn (Permission $permission): array => [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'label' => $this->permissionGroups->customLabel($permission),
                ])
                ->values()
                ->all(),
        ];
    }
}

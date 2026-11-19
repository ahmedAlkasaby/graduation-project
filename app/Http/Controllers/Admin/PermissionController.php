<?php

namespace App\Http\Controllers\Admin;

use App\Models\Permission;
use App\Services\User\PermissionGroupService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Every permission the signed in admin holds, by module, read only: the
 * Blade twin of Api\V1\Admin\PermissionController. Guarded by `roles.index`
 * in routes/admin.php.
 */
class PermissionController extends AdminController
{
    public function __construct(private readonly PermissionGroupService $permissionGroups)
    {
        parent::__construct();
        $this->setClass('permission');
    }

    public function index(): View
    {
        $grouped = $this->permissionGroups->grouped($this->admin());

        return view('admin.permissions.index', [
            'resources' => $grouped['resources']->map(fn (Collection $permissions, string $module): array => [
                'module' => $module,
                'module_name' => $this->permissionGroups->moduleLabel($module),
                'permissions' => $permissions->map(fn (Permission $permission): array => [
                    'name' => $permission->name,
                    'label' => __(Str::headline($permission->label())),
                ])->all(),
            ])->values(),
            'custom' => $grouped['custom']->map(fn (Permission $permission): array => [
                'name' => $permission->name,
                'label' => $this->permissionGroups->customLabel($permission),
            ]),
        ]);
    }
}

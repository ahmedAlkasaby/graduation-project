<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\ApiHomeController;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use App\Services\User\PermissionGroupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * The permission list the role create / edit form is built from. Guarded by
 * `roles.index` in routes/api/v1/admin.php.
 */
class PermissionController extends ApiHomeController
{
    public function __construct(private PermissionGroupService $permissionGroups)
    {
        parent::__construct();
    }

    /**
     * Each module's actions, then the custom screens (dashboard, statistics).
     */
    public function index(Request $request): JsonResponse
    {
        $grouped = $this->permissionGroups->grouped($request->user());

        return $this->successResponse([
            'resources' => $grouped['resources']
                ->map(fn (Collection $permissions, string $module): array => [
                    'module' => $module,
                    'module_name' => $this->permissionGroups->moduleLabel($module),
                    'permissions' => PermissionResource::collection($permissions),
                ])
                ->values(),
            'custom' => $grouped['custom']->map(fn (Permission $permission): array => [
                'id' => $permission->id,
                'name' => $permission->name,
                'label' => $this->permissionGroups->customLabel($permission),
            ]),
        ]);
    }
}

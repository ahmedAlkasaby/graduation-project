<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\ApiHomeController;
use App\Http\Requests\Admin\RoleFilterRequest;
use App\Http\Requests\Admin\RoleRequest;
use App\Http\Resources\RoleCollection;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Services\User\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * Managing dashboard roles and their permissions. Roles only matter for the
 * admin user type — see UserService. The form's permission list comes from
 * PermissionController.
 *
 * Access is decided by the route name: "admin.roles.index" needs the
 * "roles.index" permission, and so on (see AutoCheckPermission).
 */
class RoleController extends ApiHomeController
{
    /** Roles have no `active` flag, so the list has no status button. */
    private const ROLE_LIST_ACTIONS = [
        'is_store' => 'store',
        'is_update' => 'update',
        'is_delete' => 'destroy',
    ];

    public function __construct(private RoleService $roleService)
    {
        parent::__construct();
    }

    /** The accepted filters are listed in RoleFilterRequest. */
    public function index(RoleFilterRequest $request): JsonResponse
    {
        $query = Role::query()
            ->withCount(['users', 'permissions'])
            ->filter($request->validated());

        [$roles, $paginated] = $this->getDataWithPagination($query, $request);

        $permissions = $this->listPagePermissions('roles', self::ROLE_LIST_ACTIONS);

        return $this->collectionResponse(new RoleCollection($roles), ['permissions' => $permissions], null, $paginated);
    }

    public function store(RoleRequest $request): JsonResponse
    {
        $role = $this->roleService->create($request->validated());

        return $this->successResponse(['role' => new RoleResource($role->load('permissions'))], 'Success', Response::HTTP_CREATED);
    }

    public function show(Role $role): JsonResponse
    {
        return $this->successResponse(['role' => new RoleResource($role->load('permissions')->loadCount('users'))]);
    }

    public function update(RoleRequest $request, Role $role): JsonResponse
    {
        $role = $this->roleService->update($role, $request->validated());

        return $this->successResponse(['role' => new RoleResource($role->load('permissions'))]);
    }

    public function destroy(Role $role): JsonResponse
    {
        abort_if($this->roleService->isSystemRole($role), Response::HTTP_FORBIDDEN, __('This role cannot be deleted'));
        abort_if($this->roleService->isAssigned($role), Response::HTTP_CONFLICT, __('This role is assigned to users'));

        $this->roleService->delete($role);

        return $this->successResponse(null, __('Deleted successfully'));
    }
}

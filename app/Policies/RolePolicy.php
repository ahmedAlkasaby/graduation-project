<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use App\Services\User\RoleService;

/**
 * Who may change a role. A seeded role (config/laratrust_seeder.php) can be
 * edited only by an admin who holds it, so nobody grows a role they are not
 * in, and it is never deleted. A super admin passes every check (Gate::before).
 */
class RolePolicy
{
    public function __construct(private readonly RoleService $roleService) {}

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('roles.index');
    }

    public function view(User $user, Role $role): bool
    {
        return $user->hasPermission('roles.show');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('roles.store');
    }

    public function update(User $user, Role $role): bool
    {
        if ($this->roleService->isSystemRole($role) && ! $user->hasRole($role->name)) {
            return false;
        }

        return $user->hasPermission('roles.update');
    }

    public function delete(User $user, Role $role): bool
    {
        if ($this->roleService->isSystemRole($role)) {
            return false;
        }

        return $user->hasPermission('roles.destroy');
    }

    public function restore(User $user, Role $role): bool
    {
        return false;
    }

    public function forceDelete(User $user, Role $role): bool
    {
        return false;
    }
}

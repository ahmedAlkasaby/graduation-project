<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use App\Traits\AdminHelperTrait;

/**
 * Role 1 is the owner role. Only someone who already holds it can change it,
 * and nobody can delete it — otherwise the panel can be locked shut.
 */
class RolePolicy
{
    use AdminHelperTrait;

    public function viewAny(User $user): bool
    {
        return $user->can('roles.index');
    }

    public function view(User $user, Role $role): bool
    {
        return $user->can('roles.show');
    }

    public function create(User $user): bool
    {
        return $user->can('roles.create,roles.store');
    }

    public function update(User $user, Role $role): bool
    {
        if ($role->id === 1) {
            return $user->hasRole($role->name);
        }

        return in_array($user->type, $this->access_all_type)
            || $user->can('roles.edit,roles.update');
    }

    public function delete(User $user, Role $role): bool
    {
        if ($role->id === 1) {
            return false;
        }

        return in_array($user->type, $this->access_all_type)
            || $user->can('roles.destroy');
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

<?php

namespace App\Services\User;

use App\Models\Role;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * Creates and edits dashboard roles and the permissions they carry.
 */
class RoleService
{
    /**
     * Roles seeded from config/laratrust_seeder.php. The seeder recreates them,
     * so they are never deleted from the dashboard.
     */
    public function isSystemRole(Role $role): bool
    {
        return array_key_exists($role->name, config('laratrust_seeder.roles_structure', []));
    }

    /** Whether any user still holds $role. */
    public function isAssigned(Role $role): bool
    {
        return $role->users()->exists();
    }

    /**
     * @param  array<string, mixed>  $data  validated RoleRequest data
     */
    public function create(array $data): Role
    {
        return DB::transaction(function () use ($data): Role {
            $role = Role::create(Arr::except($data, ['permissions']));

            $role->syncPermissions($data['permissions'] ?? []);

            return $role;
        });
    }

    /**
     * Leaves the permissions alone when `permissions` is not sent at all.
     *
     * @param  array<string, mixed>  $data  validated RoleRequest data
     */
    public function update(Role $role, array $data): Role
    {
        // The seeder finds its roles by name, so a seeded role keeps its name.
        if ($this->isSystemRole($role)) {
            unset($data['name']);
        }

        return DB::transaction(function () use ($role, $data): Role {
            $role->update(Arr::except($data, ['permissions']));

            if (array_key_exists('permissions', $data)) {
                $role->syncPermissions($data['permissions'] ?? []);
            }

            return $role;
        });
    }

    public function delete(Role $role): void
    {
        DB::transaction(function () use ($role): void {
            $role->syncPermissions([]);
            $role->delete();
        });
    }
}

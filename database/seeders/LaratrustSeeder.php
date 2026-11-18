<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

/**
 * Seeds permissions and roles from config/laratrust_seeder.php, the one list
 * of both:
 *
 *  1. creates every permission in `permissions_structure` that is missing;
 *  2. creates every role in `roles_structure` and syncs it to exactly the
 *     permissions listed for it ("*" = every permission).
 *
 * Safe to run again after adding a module or a role to the config. Nothing is
 * truncated, and roles not in the config are left alone.
 */
class LaratrustSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedPermissions();
        $this->seedRoles();

        Permission::forgetRouteMap();
    }

    private function seedPermissions(): void
    {
        $labels = config('laratrust_seeder.action_labels', []);

        foreach (config('laratrust_seeder.permissions_structure', []) as $group => $modules) {
            foreach ($modules as $module => $actions) {
                foreach ($actions as $action) {
                    $permission = Permission::firstOrCreate(
                        ['name' => $this->permissionName($module, $action)],
                        ['display_name' => $labels[$action] ?? $action],
                    );

                    $permission->description = $this->permissionModule($group, $module);
                    $permission->save();
                }
            }
        }
    }

    /**
     * What a permission's `description` holds: its module for a resource
     * ("cities"), null for a custom screen ("dashboard"), which belongs to no
     * module. Kept in sync on every run, so existing rows follow the config.
     */
    private function permissionModule(string $group, string $module): ?string
    {
        return $group === 'custom' ? null : $module;
    }

    private function seedRoles(): void
    {
        foreach (config('laratrust_seeder.roles_structure', []) as $name => $definition) {
            $role = Role::firstOrCreate(
                ['name' => $name],
                ['display_name' => $definition['display_name'], 'description' => $definition['description']],
            );

            $permissions = $definition['permissions'] === '*'
                ? Permission::all()
                : Permission::query()->whereIn('name', $this->permissionNames($definition['permissions']))->get();

            $role->syncPermissions($permissions);

            $this->command?->info("Synced role {$name}: {$permissions->count()} permission(s)");
        }
    }

    /**
     * @param  array<string, list<string>>  $modules
     * @return list<string>
     */
    private function permissionNames(array $modules): array
    {
        $names = [];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                $names[] = $this->permissionName($module, $action);
            }
        }

        return $names;
    }

    /** "users" + "index" → "users.index"; an empty action is the module alone ("dashboard"). */
    private function permissionName(string $module, string $action): string
    {
        return $action === '' ? $module : "{$module}.{$action}";
    }
}

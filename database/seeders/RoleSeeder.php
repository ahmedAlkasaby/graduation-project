<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

/**
 * Role 1 is super_admin and is treated as untouchable by RolePolicy — every
 * project keeps it and adds its own roles below.
 */
class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'super_admin', 'display_name' => 'Super Admin', 'description' => 'Full access'],
            ['name' => 'admin', 'display_name' => 'Admin', 'description' => 'Admin'],
        ];

        foreach ($roles as $data) {
            $role = Role::firstOrCreate(
                ['name' => $data['name']],
                ['display_name' => $data['display_name'], 'description' => $data['description']]
            );

            if (in_array($role->name, ['super_admin', 'admin'])) {
                $role->syncPermissions(Permission::all());
            }
        }
    }
}

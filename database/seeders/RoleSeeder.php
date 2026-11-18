<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

/**
 * Roles group permissions for regular admins. A super admin is a user type,
 * not a role, so it is not seeded here. Each project adds its own roles below.
 */
class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'admin', 'display_name' => 'Admin', 'description' => 'Admin'],
        ];

        foreach ($roles as $data) {
            $role = Role::firstOrCreate(
                ['name' => $data['name']],
                ['display_name' => $data['display_name'], 'description' => $data['description']]
            );

            if ($role->name === 'admin') {
                $role->syncPermissions(Permission::all());
            }
        }
    }
}

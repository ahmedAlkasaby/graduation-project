<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * The first account. Change the credentials before deploying, or override them
 * from .env (ADMIN_EMAIL / ADMIN_PASSWORD).
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $countryId = Country::query()->value('id');

        $user = User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@example.com')],
            [
                'name_first' => 'Super',
                'name_last' => 'Admin',
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'phone' => '01000000000',
                'password' => env('ADMIN_PASSWORD', 'password'),
                'type' => 'super_admin',
                'locale' => 'en',
                'country_id' => $countryId,
                'active' => 1,
                'is_admin' => 1,
            ]
        );

        $user->syncRoles(['super_admin']);
    }
}

<?php

namespace Database\Seeders;

use App\Enums\UserTypeEnum;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * The first account, a super admin: its type alone skips every permission
 * check, so it needs no role. Change the credentials before deploying, or
 * override them from .env (ADMIN_EMAIL / ADMIN_PASSWORD).
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@example.com')],
            [
                'full_name' => 'Super Admin',
                'phone' => '01000000000',
                'password' => env('ADMIN_PASSWORD', 'password'),
                'locale' => 'en',
            ]
        );

        $user->forceFill(['type' => UserTypeEnum::SuperAdmin->value])->save();
    }
}

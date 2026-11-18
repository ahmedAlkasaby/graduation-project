<?php

namespace Database\Seeders;

use App\Enums\UserTypeEnum;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Test accounts for local development, one per kind of user the dashboard
 * handles. Every account's password is "password".
 *
 * Run after LaratrustSeeder (the admin role must exist). DatabaseSeeder only
 * calls it outside production.
 */
class UserSeeder extends Seeder
{
    public const PASSWORD = 'password';

    /** How many accounts the seeder makes in total, the 4 named ones included. */
    public const TOTAL_USERS = 50;

    public function run(): void
    {
        $admin = $this->createUser('Test Admin', 'admin.test@example.com', '01100000001', UserTypeEnum::Admin);
        $admin->syncRoles(Role::where('name', 'admin')->pluck('id')->all());

        $this->createUser('Test Super Admin', 'super.test@example.com', '01100000002', UserTypeEnum::SuperAdmin);
        $this->createUser('Test Volunteer', 'volunteer.test@example.com', '01100000003', UserTypeEnum::Volunteer);
        $this->createUser('Test Donor', 'donor.test@example.com', '01100000004', UserTypeEnum::Donor);

        for ($number = 5; $number <= self::TOTAL_USERS; $number++) {
            $this->createUser(
                fake()->name(),
                "user{$number}.test@example.com",
                sprintf('011%08d', $number),
                $number % 2 === 0 ? UserTypeEnum::Volunteer : UserTypeEnum::Donor,
            );
        }
    }

    /** Create (or refresh) one active, verified account that signs in with "password". */
    private function createUser(string $fullName, string $email, string $phone, UserTypeEnum $type): User
    {
        return User::updateOrCreate(
            ['email' => $email],
            [
                'full_name' => $fullName,
                'phone' => $phone,
                'password' => self::PASSWORD,
                'type' => $type->value,
                'active' => true,
                'locale' => 'en',
                'email_verified_at' => now(),
            ],
        );
    }
}

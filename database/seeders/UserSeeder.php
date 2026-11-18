<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Database\Factories\UserFactory;
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

    public function run(): void
    {
        $admin = $this->users()->admin()->create([
            'full_name' => 'Test Admin',
            'email' => 'admin.test@example.com',
            'phone' => '01100000001',
        ]);
        $admin->syncRoles(Role::where('name', 'admin')->pluck('id')->all());

        $this->users()->superAdmin()->create([
            'full_name' => 'Test Super Admin',
            'email' => 'super.test@example.com',
            'phone' => '01100000002',
        ]);

        $this->users()->volunteer()->create([
            'full_name' => 'Test Volunteer',
            'email' => 'volunteer.test@example.com',
            'phone' => '01100000003',
        ]);

        $this->users()->create([
            'full_name' => 'Test Donor',
            'email' => 'donor.test@example.com',
            'phone' => '01100000004',
        ]);

        $this->users()->count(10)->volunteer()->create();
        $this->users()->count(20)->create();
        $this->users()->count(3)->inactive()->create();
        $this->users()->count(2)->create()->each->delete();
    }

    /** Every seeded account signs in with the same password, whatever the factory default. */
    private function users(): UserFactory
    {
        return User::factory()->state(['password' => self::PASSWORD]);
    }
}

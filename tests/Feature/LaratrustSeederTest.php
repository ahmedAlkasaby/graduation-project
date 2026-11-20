<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\LaratrustSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/** config/laratrust_seeder.php is the one list LaratrustSeeder reads permissions and roles from. */
class LaratrustSeederTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function every_configured_permission_is_created(): void
    {
        $this->seed(LaratrustSeeder::class);

        $this->assertDatabaseHas('permissions', ['name' => 'users.restore', 'display_name' => 'restore']);
        $this->assertDatabaseHas('permissions', ['name' => 'users.destroy', 'display_name' => 'delete']);
        $this->assertDatabaseHas('permissions', ['name' => 'dashboard', 'display_name' => 'view']);
    }

    #[Test]
    public function the_admin_role_holds_every_permission(): void
    {
        $this->seed(LaratrustSeeder::class);

        $this->assertSame(Permission::count(), Role::where('name', 'admin')->first()->permissions()->count());
    }

    #[Test]
    public function every_other_role_holds_exactly_the_permissions_listed_for_it(): void
    {
        $this->seed(LaratrustSeeder::class);

        foreach (config('laratrust_seeder.roles_structure') as $name => $definition) {
            if ($definition['permissions'] === '*') {
                continue;
            }

            $expected = collect($definition['permissions'])
                ->flatMap(fn (array $actions, string $module) => array_map(
                    fn (string $action) => $action === '' ? $module : "{$module}.{$action}",
                    $actions,
                ))
                ->all();

            $this->assertEqualsCanonicalizing(
                $expected,
                Role::where('name', $name)->firstOrFail()->permissions()->pluck('name')->all(),
                "Role {$name} lists a permission that is not in permissions_structure.",
            );
        }
    }

    #[Test]
    public function running_it_again_resets_a_role_changed_from_the_dashboard(): void
    {
        $this->seed(LaratrustSeeder::class);
        $admin = Role::where('name', 'admin')->first();
        $admin->syncPermissions([]);

        $this->seed(LaratrustSeeder::class);

        $this->assertSame(Permission::count(), $admin->permissions()->count());
        $this->assertSame(1, Role::where('name', 'admin')->count());
    }

    #[Test]
    public function the_user_seeder_creates_a_test_admin_with_the_admin_role(): void
    {
        $this->seed([LaratrustSeeder::class, UserSeeder::class]);

        $this->assertTrue(User::where('email', 'admin.test@example.com')->first()->hasRole('admin'));
    }

    #[Test]
    public function every_seeded_user_signs_in_with_the_same_password(): void
    {
        $this->seed([LaratrustSeeder::class, UserSeeder::class]);

        User::withTrashed()->get()->each(
            fn (User $user) => $this->assertTrue(Hash::check(UserSeeder::PASSWORD, $user->password), "{$user->email} has another password.")
        );
    }
}

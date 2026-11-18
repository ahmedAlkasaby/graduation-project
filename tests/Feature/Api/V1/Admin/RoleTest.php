<?php

namespace Tests\Feature\Api\V1\Admin;

use App\Enums\TokenAbilityEnum;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\LaratrustSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The dashboard's role management: list, create, show, edit and delete — and
 * that a seeded or assigned role is never deleted.
 *
 * The in-memory database restarts at id 1 every test, and id 1 is a protected
 * owner account, so the acting admin is always created first.
 */
class RoleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LaratrustSeeder::class);
    }

    /**
     * @param  list<string>  $permissions
     */
    private function actingAsAdmin(array $permissions = []): User
    {
        $admin = User::factory()->admin()->create();
        $admin->givePermissions($permissions);

        Sanctum::actingAs($admin, [TokenAbilityEnum::AccessToken->value]);

        return $admin;
    }

    private function actingAsSuperAdmin(): User
    {
        $superAdmin = User::factory()->superAdmin()->create();

        Sanctum::actingAs($superAdmin, [TokenAbilityEnum::AccessToken->value]);

        return $superAdmin;
    }

    /**
     * @param  list<string>  $permissions
     */
    private function role(string $name = 'editor', array $permissions = []): Role
    {
        $role = Role::create(['name' => $name, 'display_name' => ucfirst($name)]);
        $role->syncPermissions(Permission::query()->whereIn('name', $permissions)->get());

        return $role;
    }

    /**
     * @param  list<string>  $names
     * @return list<int>
     */
    private function permissionIds(array $names): array
    {
        return Permission::query()->whereIn('name', $names)->pluck('id')->all();
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return [
            'name' => 'city_manager',
            'display_name' => 'City manager',
            'description' => 'Looks after cities',
            'permissions' => $this->permissionIds(['cities.index']),
            ...$overrides,
        ];
    }

    #[Test]
    public function guest_is_rejected(): void
    {
        $this->getJson(route('api.v1.admin.roles.index'))->assertUnauthorized();
    }

    #[Test]
    public function admin_without_the_permission_is_rejected(): void
    {
        $this->actingAsAdmin(['roles.show']);

        $this->getJson(route('api.v1.admin.roles.index'))->assertForbidden();
    }

    #[Test]
    public function admin_lists_and_searches_roles(): void
    {
        $this->actingAsAdmin(['roles.index', 'cities.index']);
        $match = $this->role('city_viewer', ['cities.index']);
        $this->role('other');

        $this->getJson(route('api.v1.admin.roles.index', ['search' => 'city']))
            ->assertOk()
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath('data.data.0.id', $match->id)
            ->assertJsonPath('data.data.0.permissions_count', 1)
            ->assertJsonPath('data.data.0.users_count', 0);
    }

    #[Test]
    public function the_list_reports_which_actions_the_admin_may_take(): void
    {
        $this->actingAsAdmin(['roles.index', 'roles.store']);

        $this->getJson(route('api.v1.admin.roles.index'))
            ->assertOk()
            ->assertJsonPath('data.permissions', [
                'is_store' => true,
                'is_update' => false,
                'is_delete' => false,
            ]);
    }

    #[Test]
    public function listing_roles_rejects_invalid_filters(): void
    {
        $this->actingAsAdmin(['roles.index']);

        $this->getJson(route('api.v1.admin.roles.index', ['limit' => 0, 'all_data' => 5]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['limit', 'all_data']);
    }

    #[Test]
    public function admin_creates_a_role_with_permissions(): void
    {
        $this->actingAsAdmin(['roles.store', 'cities.index']);

        $response = $this->postJson(route('api.v1.admin.roles.store'), $this->payload())
            ->assertCreated()
            ->assertJsonPath('data.role.name', 'city_manager')
            ->assertJsonPath('data.role.permissions.0.name', 'cities.index');

        $this->assertTrue(Role::find($response->json('data.role.id'))->hasPermission('cities.index'));
    }

    #[Test]
    public function creating_a_role_validates_the_payload(): void
    {
        $this->actingAsAdmin(['roles.store']);
        $this->role('taken');

        $this->postJson(route('api.v1.admin.roles.store'), [
            'name' => 'Not Valid!',
            'permissions' => [999],
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'display_name', 'permissions.0']);

        $this->postJson(route('api.v1.admin.roles.store'), $this->payload(['name' => 'taken', 'permissions' => []]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    #[Test]
    public function admin_shows_a_role(): void
    {
        $this->actingAsAdmin(['roles.show', 'cities.index']);
        $role = $this->role('city_viewer', ['cities.index']);

        $this->getJson(route('api.v1.admin.roles.show', $role))
            ->assertOk()
            ->assertJsonPath('data.role.id', $role->id)
            ->assertJsonPath('data.role.permissions.0.name', 'cities.index');
    }

    #[Test]
    public function admin_updates_a_role_and_its_permissions(): void
    {
        $this->actingAsAdmin(['roles.update', 'cities.index', 'cities.show']);
        $role = $this->role('city_viewer', ['cities.index']);

        $this->putJson(route('api.v1.admin.roles.update', $role), $this->payload([
            'name' => 'city_viewer',
            'display_name' => 'Renamed',
            'permissions' => $this->permissionIds(['cities.show']),
        ]))->assertOk()
            ->assertJsonPath('data.role.display_name', 'Renamed');

        $role->refresh();
        $this->assertTrue($role->hasPermission('cities.show'));
        $this->assertFalse($role->hasPermission('cities.index'));
    }

    #[Test]
    public function updating_without_permissions_keeps_them(): void
    {
        $this->actingAsAdmin(['roles.update', 'cities.index']);
        $role = $this->role('city_viewer', ['cities.index']);

        $this->putJson(route('api.v1.admin.roles.update', $role), [
            'name' => 'city_viewer',
            'display_name' => 'Renamed',
        ])->assertOk();

        $this->assertTrue($role->fresh()->hasPermission('cities.index'));
    }

    #[Test]
    public function admin_deletes_an_unused_role(): void
    {
        $this->actingAsAdmin(['roles.destroy']);
        $role = $this->role();

        $this->deleteJson(route('api.v1.admin.roles.destroy', $role))->assertOk();

        $this->assertModelMissing($role);
    }

    #[Test]
    public function a_role_held_by_a_user_is_not_deleted(): void
    {
        $this->actingAsAdmin(['roles.destroy']);
        $role = $this->role();
        User::factory()->admin()->create()->addRole($role);

        $this->deleteJson(route('api.v1.admin.roles.destroy', $role))->assertConflict();

        $this->assertModelExists($role);
    }

    #[Test]
    public function a_seeded_role_is_not_deleted(): void
    {
        $this->actingAsSuperAdmin();
        $role = Role::where('name', 'admin')->firstOrFail();

        $this->deleteJson(route('api.v1.admin.roles.destroy', $role))->assertForbidden();

        $this->assertModelExists($role);
    }
}

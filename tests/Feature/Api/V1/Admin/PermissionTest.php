<?php

namespace Tests\Feature\Api\V1\Admin;

use App\Enums\TokenAbilityEnum;
use App\Models\User;
use Database\Seeders\LaratrustSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The role form's permission list: grouped by module, CRUD actions first,
 * custom screens apart, and never more than the admin holds.
 *
 * The in-memory database restarts at id 1 every test, and id 1 is a protected
 * owner account, so the acting admin is always created first.
 */
class PermissionTest extends TestCase
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

    #[Test]
    public function a_super_admin_gets_every_permission_grouped_by_module(): void
    {
        Sanctum::actingAs(User::factory()->superAdmin()->create(), [TokenAbilityEnum::AccessToken->value]);

        $response = $this->getJson(route('api.v1.admin.permissions.index'))->assertOk();

        $resources = collect($response->json('data.resources'))->keyBy('module');

        $this->assertSame(
            ['view', 'show', 'create', 'edit', 'status', 'delete', 'restore'],
            array_column($resources['users']['permissions'], 'display_name'),
        );
        $this->assertSame('Activity Logs', $resources['activity_logs']['module_name']);
        $this->assertArrayNotHasKey('dashboard', $resources->all());

        $response->assertJsonPath('data.custom', [
            ['id' => $response->json('data.custom.0.id'), 'name' => 'dashboard', 'label' => 'Dashboard'],
            ['id' => $response->json('data.custom.1.id'), 'name' => 'statistics', 'label' => 'Statistics'],
        ]);
    }

    #[Test]
    public function an_admin_is_only_offered_the_permissions_they_hold(): void
    {
        $this->actingAsAdmin(['roles.index', 'cities.index', 'dashboard']);

        $response = $this->getJson(route('api.v1.admin.permissions.index'))->assertOk();

        $this->assertSame(
            ['cities.index', 'roles.index'],
            collect($response->json('data.resources'))->pluck('permissions.*.name')->flatten()->sort()->values()->all(),
        );
        $this->assertSame(['dashboard'], array_column($response->json('data.custom'), 'name'));
    }

    #[Test]
    public function an_admin_without_roles_index_is_refused(): void
    {
        $this->actingAsAdmin(['cities.index']);

        $this->getJson(route('api.v1.admin.permissions.index'))->assertForbidden();
    }
}

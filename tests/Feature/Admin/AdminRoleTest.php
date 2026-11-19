<?php

namespace Tests\Feature\Admin;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\LaratrustSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminRoleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LaratrustSeeder::class);
    }

    private function signInSuperAdmin(): User
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $this->actingAs($superAdmin, 'web');

        return $superAdmin;
    }

    #[Test]
    public function list_create_edit_and_show_screens_render(): void
    {
        $role = Role::query()->where('name', 'admin')->firstOrFail();
        $this->signInSuperAdmin();

        $this->get(route('admin.roles.index'))->assertOk()->assertSee('admin');
        $this->get(route('admin.roles.create'))->assertOk()->assertSee('permissions[]', false);
        $this->get(route('admin.roles.edit', $role))->assertOk();
        $this->get(route('admin.roles.show', $role))->assertOk();
        $this->get(route('admin.permissions.index'))->assertOk()->assertSee('cities');
    }

    #[Test]
    public function store_creates_a_role_with_its_permissions(): void
    {
        $permissions = Permission::query()->whereIn('name', ['cities.index', 'cities.store'])->pluck('id')->all();
        $this->signInSuperAdmin();

        $this->post(route('admin.roles.store'), [
            'name' => 'city_manager',
            'display_name' => 'City Manager',
            'permissions' => $permissions,
        ])->assertRedirect(route('admin.roles.index'))->assertSessionHas('success');

        $role = Role::query()->where('name', 'city_manager')->firstOrFail();
        $this->assertEqualsCanonicalizing(['cities.index', 'cities.store'], $role->permissions()->pluck('name')->all());
    }

    #[Test]
    public function update_with_no_box_checked_removes_every_permission(): void
    {
        $role = Role::create(['name' => 'city_manager', 'display_name' => 'City Manager']);
        $role->givePermission('cities.index');
        $this->signInSuperAdmin();

        $this->patch(route('admin.roles.update', $role), [
            'name' => 'city_manager',
            'display_name' => 'City Manager',
        ])->assertRedirect(route('admin.roles.index'));

        $this->assertSame(0, $role->permissions()->count());
    }

    #[Test]
    public function seeded_role_cannot_be_deleted(): void
    {
        $role = Role::query()->where('name', 'admin')->firstOrFail();
        $this->signInSuperAdmin();

        $this->delete(route('admin.roles.destroy', $role))->assertSessionHas('error');

        $this->assertModelExists($role);
    }

    #[Test]
    public function role_held_by_users_cannot_be_deleted(): void
    {
        $role = Role::create(['name' => 'city_manager', 'display_name' => 'City Manager']);
        User::factory()->admin()->create()->addRole($role);
        $this->signInSuperAdmin();

        $this->delete(route('admin.roles.destroy', $role))->assertSessionHas('error');

        $this->assertModelExists($role);
    }

    #[Test]
    public function unused_role_is_deleted(): void
    {
        $role = Role::create(['name' => 'city_manager', 'display_name' => 'City Manager']);
        $this->signInSuperAdmin();

        $this->delete(route('admin.roles.destroy', $role))->assertRedirect(route('admin.roles.index'));

        $this->assertModelMissing($role);
    }

    #[Test]
    public function permissions_page_needs_the_roles_index_permission(): void
    {
        $admin = User::factory()->admin()->create();
        $admin->givePermission('cities.index');

        $this->actingAs($admin, 'web')
            ->get(route('admin.permissions.index'))
            ->assertRedirect(route('admin.unauthorized'));
    }
}

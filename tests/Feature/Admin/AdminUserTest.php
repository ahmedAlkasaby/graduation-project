<?php

namespace Tests\Feature\Admin;

use App\Enums\UserTypeEnum;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\LaratrustSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LaratrustSeeder::class);

        // User id 1 is the seeded owner account, which only an owner may manage
        // (AdminHelperTrait::$access_all_id), so it is taken before each test.
        User::factory()->superAdmin()->create();
    }

    /**
     * @param  list<string>  $permissions
     */
    private function signInAdmin(array $permissions): User
    {
        $admin = User::factory()->admin()->create();
        $admin->givePermissions($permissions);
        $this->actingAs($admin, 'web');

        return $admin;
    }

    #[Test]
    public function list_hides_users_that_outrank_the_admin(): void
    {
        $donor = User::factory()->create(['full_name' => 'Donor Person']);
        User::factory()->superAdmin()->create(['full_name' => 'Boss Person']);

        $this->signInAdmin(['users.index']);

        $this->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee($donor->full_name)
            ->assertDontSee('Boss Person');
    }

    #[Test]
    public function list_is_paginated_with_the_entry_count_and_page_links(): void
    {
        User::factory()->count(3)->create();
        $this->signInAdmin(['users.index']);

        $this->get(route('admin.users.index', ['limit' => 2]))
            ->assertOk()
            ->assertSeeText('Showing 1 to 2 of 4 entries')
            ->assertSee('class="pagination', false)
            ->assertSee(route('admin.users.index', ['limit' => 2, 'page' => 2]));
    }

    #[Test]
    public function create_edit_and_show_screens_render(): void
    {
        $donor = User::factory()->create();
        $this->signInAdmin(['users.show', 'users.store', 'users.update']);

        $this->get(route('admin.users.create'))->assertOk();
        $this->get(route('admin.users.edit', $donor))->assertOk()->assertSee($donor->full_name)->assertDontSee($donor->password);
        $this->get(route('admin.users.show', $donor))->assertOk()->assertSee($donor->full_name);
    }

    #[Test]
    public function store_creates_an_admin_with_roles(): void
    {
        $role = Role::query()->where('name', 'admin')->firstOrFail();
        $this->signInAdmin(['users.store']);

        $this->post(route('admin.users.store'), [
            'full_name' => 'New Admin',
            'email' => 'new.admin@example.com',
            'phone' => '01012345678',
            'type' => UserTypeEnum::Admin->value,
            'roles' => [$role->id],
            'password' => 'secret-password',
            'confirm_password' => 'secret-password',
            'locale' => 'en',
            'active' => 1,
            'is_notify' => 1,
        ])->assertRedirect(route('admin.users.index'))->assertSessionHas('success');

        $user = User::query()->where('email', 'new.admin@example.com')->firstOrFail();
        $this->assertTrue($user->hasRole('admin'));
    }

    #[Test]
    public function store_refuses_a_type_that_outranks_the_admin(): void
    {
        $this->signInAdmin(['users.store']);

        $this->post(route('admin.users.store'), [
            'full_name' => 'Wannabe Boss',
            'email' => 'boss@example.com',
            'phone' => '01012345679',
            'type' => UserTypeEnum::SuperAdmin->value,
            'password' => 'secret-password',
            'confirm_password' => 'secret-password',
        ])->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'boss@example.com']);
    }

    #[Test]
    public function update_keeps_the_password_when_left_empty(): void
    {
        $donor = User::factory()->create();
        $oldHash = $donor->password;
        $this->signInAdmin(['users.update']);

        $this->patch(route('admin.users.update', $donor), [
            'full_name' => 'Renamed Donor',
            'phone' => $donor->phone,
            'type' => UserTypeEnum::Donor->value,
        ])->assertRedirect(route('admin.users.index'));

        $this->assertSame('Renamed Donor', $donor->fresh()->full_name);
        $this->assertSame($oldHash, $donor->fresh()->password);
    }

    #[Test]
    public function admin_cannot_deactivate_or_delete_themselves(): void
    {
        $admin = $this->signInAdmin(['users.status', 'users.destroy']);

        $this->patchJson(route('admin.users.status', $admin))->assertForbidden();
        $this->delete(route('admin.users.destroy', $admin))->assertSessionHas('error');

        $this->assertTrue((bool) $admin->fresh()->active);
        $this->assertNotSoftDeleted($admin);
    }

    #[Test]
    public function status_destroy_and_restore_a_user(): void
    {
        $donor = User::factory()->create();
        $this->signInAdmin(['users.status', 'users.destroy', 'users.restore']);

        $this->patchJson(route('admin.users.status', $donor))->assertOk()->assertJson(['active' => false]);

        $this->delete(route('admin.users.destroy', $donor))->assertRedirect(route('admin.users.index'));
        $this->assertSoftDeleted($donor);

        $this->patch(route('admin.users.restore', $donor))->assertRedirect(route('admin.users.index'));
        $this->assertNotSoftDeleted($donor);
    }
}

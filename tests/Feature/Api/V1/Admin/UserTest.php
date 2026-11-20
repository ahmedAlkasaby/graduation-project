<?php

namespace Tests\Feature\Api\V1\Admin;

use App\Enums\TokenAbilityEnum;
use App\Enums\UserTypeEnum;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\LaratrustSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The dashboard's user management: list, create, show, edit, status, delete
 * and restore — and that only the admin type ever carries roles.
 *
 * The in-memory database restarts at id 1 every test, and id 1 is a protected
 * owner account, so the acting admin is always created first.
 */
class UserTest extends TestCase
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

    private function role(string $name = 'editor'): Role
    {
        return Role::create(['name' => $name, 'display_name' => ucfirst($name)]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return [
            'full_name' => 'Mona Ali',
            'email' => 'mona@example.com',
            'phone' => '01012345678',
            'password' => 'secret123',
            'confirm_password' => 'secret123',
            'type' => UserTypeEnum::Donor->value,
            'active' => true,
            ...$overrides,
        ];
    }

    #[Test]
    public function guest_is_rejected(): void
    {
        $this->getJson(route('api.v1.admin.users.index'))->assertUnauthorized();
    }

    #[Test]
    public function admin_without_the_permission_is_rejected(): void
    {
        $this->actingAsAdmin(['users.show']);

        $this->getJson(route('api.v1.admin.users.index'))->assertForbidden();
    }

    #[Test]
    public function admin_lists_users_filtered_by_type(): void
    {
        $this->actingAsAdmin(['users.index']);
        User::factory()->count(2)->create();
        User::factory()->volunteer()->create();

        $this->getJson(route('api.v1.admin.users.index', ['type' => UserTypeEnum::Donor->value]))
            ->assertOk()
            ->assertJsonCount(2, 'data.data');
    }

    #[Test]
    public function admin_lists_only_deleted_users_from_the_trash(): void
    {
        $this->actingAsAdmin(['users.index']);
        User::factory()->create();
        $deleted = User::factory()->create();
        $deleted->delete();

        $this->getJson(route('api.v1.admin.users.index', ['trashed' => 'only']))
            ->assertOk()
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath('data.data.0.id', $deleted->id);
    }

    #[Test]
    public function form_options_offer_only_the_types_the_admin_may_manage(): void
    {
        $this->actingAsAdmin(['users.store']);
        $this->role();

        $response = $this->getJson(route('api.v1.admin.users.form-options'))
            ->assertOk()
            ->assertJsonPath('data.roles_for_types', [UserTypeEnum::Admin->value])
            ->assertJsonPath('data.email_required_for', UserTypeEnum::adminValues());

        $this->assertEqualsCanonicalizing(
            [...array_keys(config('laratrust_seeder.roles_structure')), 'editor'],
            array_column($response->json('data.roles'), 'name'),
        );

        $this->assertSame(
            [UserTypeEnum::Donor->value, UserTypeEnum::Volunteer->value, UserTypeEnum::Admin->value],
            array_column($response->json('data.types'), 'value'),
        );
    }

    #[Test]
    public function a_super_admin_is_offered_every_type(): void
    {
        Sanctum::actingAs(User::factory()->superAdmin()->create(), [TokenAbilityEnum::AccessToken->value]);

        $types = $this->getJson(route('api.v1.admin.users.form-options'))->assertOk()->json('data.types');

        $this->assertSame(UserTypeEnum::values(), array_column($types, 'value'));
    }

    #[Test]
    public function form_options_are_open_to_an_admin_who_may_only_edit(): void
    {
        $this->actingAsAdmin(['users.update']);

        $this->getJson(route('api.v1.admin.users.form-options'))->assertOk();
    }

    #[Test]
    public function form_options_need_the_create_or_edit_permission(): void
    {
        $this->actingAsAdmin(['users.index']);

        $this->getJson(route('api.v1.admin.users.form-options'))->assertForbidden();
    }

    #[Test]
    public function the_list_reports_which_actions_the_admin_may_take(): void
    {
        $this->actingAsAdmin(['users.index', 'users.store', 'users.status']);

        $this->getJson(route('api.v1.admin.users.index'))
            ->assertOk()
            ->assertJsonPath('data.permissions', [
                'is_store' => true,
                'is_update' => false,
                'is_delete' => false,
                'is_status' => true,
                'is_restore' => false,
            ]);
    }

    #[Test]
    public function a_super_admin_may_take_every_action_on_the_list(): void
    {
        Sanctum::actingAs(User::factory()->superAdmin()->create(), [TokenAbilityEnum::AccessToken->value]);

        $this->getJson(route('api.v1.admin.users.index'))
            ->assertOk()
            ->assertJsonPath('data.permissions', [
                'is_store' => true,
                'is_update' => true,
                'is_delete' => true,
                'is_status' => true,
                'is_restore' => true,
            ]);
    }

    #[Test]
    public function the_list_hides_users_the_admin_may_not_manage(): void
    {
        $admin = $this->actingAsAdmin(['users.index']);
        $donor = User::factory()->create();
        User::factory()->superAdmin()->create();

        $ids = array_column($this->getJson(route('api.v1.admin.users.index'))->assertOk()->json('data.data'), 'id');

        $this->assertEqualsCanonicalizing([$admin->id, $donor->id], $ids);
    }

    #[Test]
    public function the_list_hides_the_owner_from_other_super_admins(): void
    {
        $owner = User::factory()->superAdmin()->create(['id' => 1]);
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [TokenAbilityEnum::AccessToken->value]);

        $ids = array_column($this->getJson(route('api.v1.admin.users.index'))->assertOk()->json('data.data'), 'id');

        $this->assertNotContains($owner->id, $ids);
        $this->assertContains($superAdmin->id, $ids);
    }

    #[Test]
    public function the_owner_sees_every_user(): void
    {
        $owner = User::factory()->superAdmin()->create(['id' => 1]);
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($owner, [TokenAbilityEnum::AccessToken->value]);

        $ids = array_column($this->getJson(route('api.v1.admin.users.index'))->assertOk()->json('data.data'), 'id');

        $this->assertEqualsCanonicalizing([$owner->id, $superAdmin->id], $ids);
    }

    #[Test]
    public function admin_lists_users_filtered_by_active(): void
    {
        $this->actingAsAdmin(['users.index']);
        $inactive = User::factory()->inactive()->create();

        $this->getJson(route('api.v1.admin.users.index', ['active' => '0']))
            ->assertOk()
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath('data.data.0.id', $inactive->id);
    }

    #[Test]
    public function listing_users_rejects_invalid_filters(): void
    {
        $this->actingAsAdmin(['users.index']);

        $this->getJson(route('api.v1.admin.users.index', [
            'type' => 'hacker',
            'active' => '5',
            'trashed' => 'yes',
            'date_start' => '2026-05-01',
            'date_end' => '2026-01-01',
            'limit' => 0,
        ]))->assertUnprocessable()
            ->assertJsonValidationErrors(['type', 'active', 'trashed', 'date_end', 'limit']);
    }

    #[Test]
    public function admin_searches_users_by_name_email_or_phone(): void
    {
        $this->actingAsAdmin(['users.index']);
        $match = User::factory()->create(['full_name' => 'Zeinab Hassan']);
        User::factory()->create(['full_name' => 'Omar Said']);

        $this->getJson(route('api.v1.admin.users.index', ['search' => 'Zeinab']))
            ->assertOk()
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath('data.data.0.id', $match->id);
    }

    #[Test]
    public function creating_a_donor_never_stores_roles(): void
    {
        $this->actingAsAdmin(['users.store']);
        $role = $this->role();

        $response = $this->postJson(route('api.v1.admin.users.store'), $this->payload([
            'roles' => [$role->id],
        ]))->assertCreated()
            ->assertJsonPath('data.user.type', UserTypeEnum::Donor->value)
            ->assertJsonCount(0, 'data.user.roles');

        $this->assertFalse(User::find($response->json('data.user.id'))->hasRole('editor'));
    }

    #[Test]
    public function creating_a_donor_ignores_invalid_role_ids(): void
    {
        $this->actingAsAdmin(['users.store']);

        $this->postJson(route('api.v1.admin.users.store'), $this->payload([
            'roles' => [999],
        ]))->assertCreated();
    }

    #[Test]
    public function creating_an_admin_assigns_the_roles_sent(): void
    {
        $this->actingAsAdmin(['users.store']);
        $role = $this->role();

        $response = $this->postJson(route('api.v1.admin.users.store'), $this->payload([
            'type' => UserTypeEnum::Admin->value,
            'roles' => [$role->id],
        ]))->assertCreated()
            ->assertJsonPath('data.user.roles.0.name', 'editor');

        $this->assertTrue(User::find($response->json('data.user.id'))->hasRole('editor'));
    }

    #[Test]
    public function creating_an_admin_validates_the_role_ids(): void
    {
        $this->actingAsAdmin(['users.store']);

        $this->postJson(route('api.v1.admin.users.store'), $this->payload([
            'type' => UserTypeEnum::Admin->value,
            'roles' => [999],
        ]))->assertUnprocessable()
            ->assertJsonValidationErrors(['roles.0']);
    }

    #[Test]
    public function creating_a_user_validates_the_payload(): void
    {
        $this->actingAsAdmin(['users.store']);

        $this->postJson(route('api.v1.admin.users.store'), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['full_name', 'type', 'phone', 'password']);
    }

    #[Test]
    public function an_admin_needs_an_email(): void
    {
        $this->actingAsAdmin(['users.store']);

        $this->postJson(route('api.v1.admin.users.store'), $this->payload([
            'type' => UserTypeEnum::Admin->value,
            'email' => null,
        ]))->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function admin_cannot_create_a_super_admin(): void
    {
        $this->actingAsAdmin(['users.store']);

        $this->postJson(route('api.v1.admin.users.store'), $this->payload([
            'type' => UserTypeEnum::SuperAdmin->value,
        ]))->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'mona@example.com']);
    }

    #[Test]
    public function admin_shows_a_user(): void
    {
        $this->actingAsAdmin(['users.show']);
        $user = User::factory()->create();

        $this->getJson(route('api.v1.admin.users.show', $user))
            ->assertOk()
            ->assertJsonPath('data.user.id', $user->id);
    }

    #[Test]
    public function admin_cannot_see_a_super_admin(): void
    {
        $this->actingAsAdmin(['users.show']);
        $superAdmin = User::factory()->superAdmin()->create();

        $this->getJson(route('api.v1.admin.users.show', $superAdmin))->assertForbidden();
    }

    #[Test]
    public function changing_an_admin_into_a_donor_removes_their_roles(): void
    {
        $this->actingAsAdmin(['users.update']);
        $role = $this->role();
        $user = User::factory()->admin()->create();
        $user->addRole($role);

        $this->putJson(route('api.v1.admin.users.update', $user), $this->payload([
            'email' => $user->email,
            'phone' => $user->phone,
            'password' => null,
            'confirm_password' => null,
            'type' => UserTypeEnum::Donor->value,
        ]))->assertOk()
            ->assertJsonCount(0, 'data.user.roles');

        $this->assertFalse($user->fresh()->hasRole('editor'));
    }

    #[Test]
    public function updating_an_admin_without_roles_keeps_their_roles(): void
    {
        $this->actingAsAdmin(['users.update']);
        $role = $this->role();
        $user = User::factory()->admin()->create();
        $user->addRole($role);

        $this->putJson(route('api.v1.admin.users.update', $user), $this->payload([
            'full_name' => 'Renamed',
            'email' => $user->email,
            'phone' => $user->phone,
            'password' => null,
            'confirm_password' => null,
            'type' => UserTypeEnum::Admin->value,
        ]))->assertOk()
            ->assertJsonPath('data.user.full_name', 'Renamed')
            ->assertJsonPath('data.user.roles.0.name', 'editor');
    }

    #[Test]
    public function updating_without_a_password_keeps_the_old_one(): void
    {
        $this->actingAsAdmin(['users.update']);
        $user = User::factory()->create();
        $oldHash = $user->password;

        $this->putJson(route('api.v1.admin.users.update', $user), $this->payload([
            'email' => $user->email,
            'phone' => $user->phone,
            'password' => null,
            'confirm_password' => null,
        ]))->assertOk();

        $this->assertSame($oldHash, $user->fresh()->password);
    }

    #[Test]
    public function admin_cannot_change_their_own_type(): void
    {
        $admin = $this->actingAsAdmin(['users.update']);

        $this->putJson(route('api.v1.admin.users.update', $admin), $this->payload([
            'email' => $admin->email,
            'phone' => $admin->phone,
            'password' => null,
            'confirm_password' => null,
            'type' => UserTypeEnum::Donor->value,
        ]))->assertForbidden();

        $this->assertSame(UserTypeEnum::Admin->value, $admin->fresh()->type);
    }

    #[Test]
    public function deactivating_a_user_revokes_their_tokens(): void
    {
        $this->actingAsAdmin(['users.status']);
        $user = User::factory()->create();
        $user->createToken('app');

        $this->patchJson(route('api.v1.admin.users.status', $user))
            ->assertOk()
            ->assertJsonPath('data.user.active', 0);

        $this->assertFalse($user->fresh()->active);
        $this->assertSame(0, $user->tokens()->count());
    }

    #[Test]
    public function admin_cannot_deactivate_themselves(): void
    {
        $admin = $this->actingAsAdmin(['users.status']);

        $this->patchJson(route('api.v1.admin.users.status', $admin))->assertForbidden();
    }

    #[Test]
    public function admin_soft_deletes_a_user(): void
    {
        $this->actingAsAdmin(['users.destroy']);
        $user = User::factory()->create();

        $this->deleteJson(route('api.v1.admin.users.destroy', $user))->assertOk();

        $this->assertSoftDeleted($user);
    }

    #[Test]
    public function admin_cannot_delete_themselves(): void
    {
        $admin = $this->actingAsAdmin(['users.destroy']);

        $this->deleteJson(route('api.v1.admin.users.destroy', $admin))->assertForbidden();

        $this->assertNotSoftDeleted($admin);
    }

    #[Test]
    public function the_owner_account_is_protected_from_other_admins(): void
    {
        $owner = User::factory()->superAdmin()->create(['id' => 1]);
        Sanctum::actingAs(User::factory()->superAdmin()->create(), [TokenAbilityEnum::AccessToken->value]);

        $this->deleteJson(route('api.v1.admin.users.destroy', $owner))->assertForbidden();
    }

    #[Test]
    public function admin_restores_a_deleted_user(): void
    {
        $this->actingAsAdmin(['users.restore']);
        $user = User::factory()->create();
        $user->delete();

        $this->patchJson(route('api.v1.admin.users.restore', $user))->assertOk();

        $this->assertNotSoftDeleted($user);
    }

    #[Test]
    public function showing_a_missing_user_returns_not_found(): void
    {
        $this->actingAsAdmin(['users.show']);

        $this->getJson(route('api.v1.admin.users.show', 999))->assertNotFound();
    }
}

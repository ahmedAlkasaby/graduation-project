<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\LaratrustSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RoleLoginPlatformTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LaratrustSeeder::class);
    }

    /**
     * @param  array{can_login_web?: bool, can_login_mobile?: bool}  $platforms
     */
    private function adminWithRole(array $platforms): User
    {
        $role = Role::create(['name' => 'field_admin', 'display_name' => 'Field Admin'] + $platforms);
        $admin = User::factory()->admin()->create();
        $admin->addRole($role);

        return $admin;
    }

    /**
     * @return array<string, string>
     */
    private function apiLoginPayload(User $user): array
    {
        return [
            'email' => $user->email,
            'password' => 'password',
            'imei' => 'test-imei',
            'token' => 'test-device-token',
            'device_type' => 'android',
        ];
    }

    #[Test]
    public function role_form_saves_the_login_platforms(): void
    {
        $this->actingAs(User::factory()->superAdmin()->create(), 'web');

        $this->post(route('admin.roles.store'), [
            'name' => 'field_admin',
            'display_name' => 'Field Admin',
            'can_login_web' => '0',
            'can_login_mobile' => '1',
        ])->assertRedirect(route('admin.roles.index'));

        $role = Role::query()->where('name', 'field_admin')->firstOrFail();
        $this->assertFalse($role->can_login_web);
        $this->assertTrue($role->can_login_mobile);

        $this->get(route('admin.roles.index'))->assertOk()->assertSee(__('Not allowed'));
    }

    #[Test]
    public function admin_whose_role_blocks_the_web_cannot_sign_in_to_the_panel_or_the_dashboard_api(): void
    {
        $admin = $this->adminWithRole(['can_login_web' => false, 'can_login_mobile' => true]);

        $this->post(route('admin.login.save'), ['email' => $admin->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest('web');

        $this->postJson(route('api.v1.admin.login'), $this->apiLoginPayload($admin))->assertUnauthorized();
        $this->postJson(route('api.v1.public.login'), $this->apiLoginPayload($admin))->assertOk();
    }

    #[Test]
    public function signed_in_admin_is_signed_out_once_the_web_is_blocked(): void
    {
        $admin = $this->adminWithRole(['can_login_web' => false]);

        $this->actingAs($admin, 'web')
            ->get(route('admin.index'))
            ->assertRedirect(route('admin.login'))
            ->assertSessionHas('error');
    }

    #[Test]
    public function admin_whose_role_blocks_the_app_cannot_sign_in_through_the_app_login(): void
    {
        $admin = $this->adminWithRole(['can_login_web' => true, 'can_login_mobile' => false]);

        $this->postJson(route('api.v1.public.login'), $this->apiLoginPayload($admin))->assertUnauthorized();
        $this->postJson(route('api.v1.admin.login'), $this->apiLoginPayload($admin))->assertOk();
    }

    #[Test]
    public function seeded_role_keeps_its_name_and_is_edited_only_by_its_holders(): void
    {
        $seeded = Role::query()->where('name', 'admin')->firstOrFail();

        $outsider = $this->adminWithRole([]);
        $outsider->givePermission('roles.update');
        $this->actingAs($outsider, 'web');

        $this->get(route('admin.roles.edit', $seeded))->assertRedirect(route('admin.unauthorized'));
        $this->patch(route('admin.roles.update', $seeded), ['name' => 'admin', 'display_name' => 'Hijacked'])
            ->assertRedirect(route('admin.unauthorized'));
        $this->assertSame('Admin', $seeded->fresh()->display_name);

        $this->actingAs(User::factory()->superAdmin()->create(), 'web');

        $this->patch(route('admin.roles.update', $seeded), ['name' => 'renamed', 'display_name' => 'Admin'])
            ->assertRedirect(route('admin.roles.index'));
        $this->assertSame('admin', $seeded->fresh()->name);
    }
}

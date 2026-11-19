<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\LaratrustSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LaratrustSeeder::class);
    }

    #[Test]
    public function guest_is_sent_to_the_login_page(): void
    {
        $this->get(route('admin.index'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.cities.index'))->assertRedirect(route('admin.login'));
    }

    #[Test]
    public function login_page_renders(): void
    {
        $this->get(route('admin.login'))->assertOk()->assertSee(route('admin.login.save'), false);
    }

    #[Test]
    public function admin_signs_in_with_email_or_phone(): void
    {
        $admin = User::factory()->admin()->create();

        $this->post(route('admin.login.save'), ['email' => $admin->email, 'password' => 'password'])
            ->assertRedirect(route('admin.index'));
        $this->assertAuthenticatedAs($admin, 'web');

        $this->post(route('admin.logout'))->assertRedirect(route('admin.login'));
        $this->assertGuest('web');

        $this->post(route('admin.login.save'), ['email' => $admin->phone, 'password' => 'password'])
            ->assertRedirect(route('admin.index'));
        $this->assertAuthenticatedAs($admin, 'web');
    }

    #[Test]
    public function wrong_password_is_refused(): void
    {
        $admin = User::factory()->admin()->create();

        $this->from(route('admin.login'))
            ->post(route('admin.login.save'), ['email' => $admin->email, 'password' => 'wrong-password'])
            ->assertRedirect(route('admin.login'))
            ->assertSessionHasErrors('email');
        $this->assertGuest('web');
    }

    #[Test]
    public function app_user_cannot_sign_in(): void
    {
        $donor = User::factory()->create();

        $this->post(route('admin.login.save'), ['email' => $donor->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest('web');
    }

    #[Test]
    public function inactive_admin_cannot_sign_in(): void
    {
        $admin = User::factory()->admin()->inactive()->create();

        $this->post(route('admin.login.save'), ['email' => $admin->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest('web');
    }

    #[Test]
    public function app_user_with_a_session_is_signed_out(): void
    {
        $donor = User::factory()->create();

        $this->actingAs($donor, 'web')
            ->get(route('admin.index'))
            ->assertRedirect(route('admin.login'));
        $this->assertGuest('web');
    }

    #[Test]
    public function any_admin_opens_the_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin, 'web')->get(route('admin.index'))->assertOk();
    }

    #[Test]
    public function admin_without_the_permission_is_sent_to_the_unauthorized_page(): void
    {
        $admin = User::factory()->admin()->create();
        $admin->givePermission('cities.index');

        $this->actingAs($admin, 'web')
            ->get(route('admin.regions.index'))
            ->assertRedirect(route('admin.unauthorized'));
    }

    #[Test]
    public function create_and_edit_screens_need_the_store_and_update_permissions(): void
    {
        $admin = User::factory()->admin()->create();
        $admin->givePermission('cities.index');

        $this->actingAs($admin, 'web')->get(route('admin.cities.create'))->assertRedirect(route('admin.unauthorized'));

        $admin->givePermission('cities.store');

        $this->actingAs($admin->fresh(), 'web')->get(route('admin.cities.create'))->assertOk();
    }

    #[Test]
    public function super_admin_opens_every_list_page(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        foreach (['cities', 'regions', 'users', 'roles', 'permissions'] as $module) {
            $this->actingAs($superAdmin, 'web')->get(route("admin.{$module}.index"))->assertOk();
        }
    }

    #[Test]
    public function admin_saves_their_theme_and_language(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin, 'web')->postJson(route('admin.theme.change'), ['theme' => 'dark'])->assertOk();
        $this->actingAs($admin, 'web')->postJson(route('admin.lang.change'), ['lang' => 'ar'])->assertOk();

        $this->assertDatabaseHas('users', ['id' => $admin->id, 'theme' => 'dark', 'locale' => 'ar']);
    }

    #[Test]
    public function the_api_still_answers_with_json(): void
    {
        $this->get(route('api.v1.admin.cities.index'))->assertUnauthorized()->assertJsonPath('status', 'unauthenticated');
    }
}

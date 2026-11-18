<?php

namespace Tests\Feature\Api\V1\Public;

use App\Enums\TokenAbilityEnum;
use App\Models\User;
use Database\Seeders\LaratrustSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The dashboard sidebar flags for the signed in user.
 *
 * The in-memory database restarts at id 1 every test, and id 1 is a protected
 * owner account, so the acting user is always created first.
 */
class ProfilePermissionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LaratrustSeeder::class);
    }

    #[Test]
    public function an_admin_sees_only_the_sidebar_items_their_permissions_open(): void
    {
        $admin = User::factory()->admin()->create();
        $admin->givePermissions(['dashboard', 'users.index', 'users.store']);

        Sanctum::actingAs($admin, [TokenAbilityEnum::AccessToken->value]);

        $this->getJson(route('api.v1.public.profile.permissions'))
            ->assertOk()
            ->assertExactJson([
                'status' => 'success',
                'message' => 'Success',
                'data' => [
                    'is_users' => true,
                    'is_roles' => false,
                ],
            ]);
    }

    #[Test]
    public function a_super_admin_sees_every_sidebar_item(): void
    {
        Sanctum::actingAs(User::factory()->superAdmin()->create(), [TokenAbilityEnum::AccessToken->value]);

        $flags = $this->getJson(route('api.v1.public.profile.permissions'))->assertOk()->json('data');

        $this->assertNotContains(false, $flags);
    }

    #[Test]
    public function a_guest_is_refused(): void
    {
        $this->getJson(route('api.v1.public.profile.permissions'))->assertUnauthorized();
    }
}

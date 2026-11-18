<?php

namespace Tests\Feature\Api\V1\Admin;

use App\Enums\TokenAbilityEnum;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * A route may pass its permission to the middleware directly instead of
 * relying on its name. These routes mirror the admin group's middleware and
 * add an explicit permission on top, the way a real admin route would.
 */
class AutoCheckPermissionTest extends TestCase
{
    use RefreshDatabase;

    private const GROUP_MIDDLEWARE = ['auth:sanctum', 'admin', 'auto-check-permission'];

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);

        Route::middleware(self::GROUP_MIDDLEWARE)
            ->get('api/v1/admin/_test/explicit', fn () => response()->json(['ok' => true]))
            ->name('api.v1.admin._test.explicit')
            ->permission('cities.show');

        Route::middleware(self::GROUP_MIDDLEWARE)
            ->get('api/v1/admin/_test/any-of', fn () => response()->json(['ok' => true]))
            ->name('api.v1.admin._test.any_of')
            ->permission('cities.show', 'cities.update');

        Route::middleware(self::GROUP_MIDDLEWARE)
            ->get('api/v1/admin/_test/unclaimed', fn () => response()->json(['ok' => true]))
            ->name('api.v1.admin._test.unclaimed');
    }

    /**
     * @param  list<string>  $permissions
     */
    private function actingAsAdmin(array $permissions = []): void
    {
        $admin = User::factory()->admin()->create();
        $admin->givePermissions($permissions);

        Sanctum::actingAs($admin, [TokenAbilityEnum::AccessToken->value]);
    }

    #[Test]
    public function explicit_permission_grants_access_when_the_route_name_claims_none(): void
    {
        $this->actingAsAdmin(['cities.show']);

        $this->getJson('api/v1/admin/_test/explicit')->assertOk();
    }

    #[Test]
    public function explicit_permission_is_required(): void
    {
        $this->actingAsAdmin(['cities.index']);

        $this->getJson('api/v1/admin/_test/explicit')->assertForbidden();
    }

    #[Test]
    public function any_one_of_several_explicit_permissions_is_enough(): void
    {
        $this->actingAsAdmin(['cities.update']);

        $this->getJson('api/v1/admin/_test/any-of')->assertOk();
    }

    #[Test]
    public function super_admin_skips_the_permission_check(): void
    {
        Sanctum::actingAs(User::factory()->superAdmin()->create(), [TokenAbilityEnum::AccessToken->value]);

        $this->getJson('api/v1/admin/_test/unclaimed')->assertOk();
    }

    #[Test]
    public function route_without_explicit_or_named_permission_is_refused(): void
    {
        $this->actingAsAdmin(['cities.index', 'cities.show']);

        $this->getJson('api/v1/admin/_test/unclaimed')->assertForbidden();
    }
}

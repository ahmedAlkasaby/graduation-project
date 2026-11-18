<?php

namespace Tests\Feature\Api\V1\Admin;

use App\Enums\TokenAbilityEnum;
use App\Models\City;
use App\Models\Country;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);
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
    public function guest_is_rejected(): void
    {
        $this->getJson(route('api.v1.admin.cities.index'))->assertUnauthorized();
    }

    #[Test]
    public function app_user_is_rejected_even_with_the_permission(): void
    {
        $donor = User::factory()->create();
        $donor->givePermission('cities.index');
        Sanctum::actingAs($donor, [TokenAbilityEnum::AccessToken->value]);

        $this->getJson(route('api.v1.admin.cities.index'))->assertForbidden();
    }

    #[Test]
    public function admin_without_the_permission_is_rejected(): void
    {
        $this->actingAsAdmin(['cities.show']);

        $this->getJson(route('api.v1.admin.cities.index'))->assertForbidden();
    }

    #[Test]
    public function inactive_admin_is_rejected(): void
    {
        $admin = User::factory()->admin()->inactive()->create();
        $admin->givePermission('cities.index');
        Sanctum::actingAs($admin, [TokenAbilityEnum::AccessToken->value]);

        $this->getJson(route('api.v1.admin.cities.index'))->assertForbidden();
    }

    #[Test]
    public function refresh_token_cannot_reach_admin_routes(): void
    {
        $admin = User::factory()->admin()->create();
        $admin->givePermission('cities.index');
        Sanctum::actingAs($admin, [TokenAbilityEnum::RefreshToken->value]);

        $this->getJson(route('api.v1.admin.cities.index'))->assertForbidden();
    }

    #[Test]
    public function admin_with_the_permission_lists_active_and_inactive_cities(): void
    {
        City::factory()->create();
        City::factory()->inactive()->create();
        $this->actingAsAdmin(['cities.index']);

        $this->getJson(route('api.v1.admin.cities.index'))
            ->assertOk()
            ->assertJsonCount(2, 'data.data');
    }

    #[Test]
    public function super_admin_passes_every_permission_check(): void
    {
        Sanctum::actingAs(User::factory()->superAdmin()->create(), [TokenAbilityEnum::AccessToken->value]);

        $this->getJson(route('api.v1.admin.cities.index'))->assertOk();
    }

    #[Test]
    public function admin_creates_a_city(): void
    {
        $country = Country::factory()->create();
        $this->actingAsAdmin(['cities.store']);

        $this->postJson(route('api.v1.admin.cities.store'), [
            'name' => ['en' => 'Cairo', 'ar' => 'القاهرة'],
            'country_id' => $country->id,
            'active' => true,
        ])->assertCreated()->assertJsonPath('data.city.name', 'Cairo');

        $this->assertDatabaseCount('cities', 1);
    }

    #[Test]
    public function creating_a_city_validates_the_payload(): void
    {
        $this->actingAsAdmin(['cities.store']);

        $this->postJson(route('api.v1.admin.cities.store'), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'country_id', 'active']);
    }

    #[Test]
    public function admin_updates_a_city(): void
    {
        $city = City::factory()->create();
        $this->actingAsAdmin(['cities.update']);

        $this->putJson(route('api.v1.admin.cities.update', $city), [
            'name' => ['en' => 'Giza', 'ar' => 'الجيزة'],
            'country_id' => $city->country_id,
            'active' => true,
        ])->assertOk()->assertJsonPath('data.city.name', 'Giza');
    }

    #[Test]
    public function admin_toggles_a_city_status(): void
    {
        $city = City::factory()->create();
        $this->actingAsAdmin(['cities.status']);

        $this->patchJson(route('api.v1.admin.cities.status', $city))
            ->assertOk()
            ->assertJsonPath('data.city.active', 0);

        $this->assertSame(0, (int) $city->fresh()->active);
    }

    #[Test]
    public function admin_deletes_a_city(): void
    {
        $city = City::factory()->create();
        $this->actingAsAdmin(['cities.destroy']);

        $this->deleteJson(route('api.v1.admin.cities.destroy', $city))->assertOk();

        $this->assertModelMissing($city);
    }

    #[Test]
    public function showing_a_missing_city_returns_not_found(): void
    {
        $this->actingAsAdmin(['cities.show']);

        $this->getJson(route('api.v1.admin.cities.show', 999))->assertNotFound();
    }
}

<?php

namespace Tests\Feature\Api\V1\Admin;

use App\Enums\TokenAbilityEnum;
use App\Models\City;
use App\Models\Region;
use App\Models\User;
use Database\Seeders\LaratrustSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RegionTest extends TestCase
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
    public function guest_is_rejected(): void
    {
        $this->getJson(route('api.v1.admin.regions.index'))->assertUnauthorized();
    }

    #[Test]
    public function admin_without_the_permission_is_rejected(): void
    {
        $this->actingAsAdmin(['regions.show']);

        $this->getJson(route('api.v1.admin.regions.index'))->assertForbidden();
    }

    #[Test]
    public function admin_with_the_permission_lists_active_and_inactive_regions(): void
    {
        Region::factory()->create();
        Region::factory()->inactive()->create();
        $this->actingAsAdmin(['regions.index']);

        $this->getJson(route('api.v1.admin.regions.index'))
            ->assertOk()
            ->assertJsonCount(2, 'data.data');
    }

    #[Test]
    public function admin_filters_regions_by_city(): void
    {
        $city = City::factory()->create();
        Region::factory()->count(2)->for($city)->create();
        Region::factory()->create();
        $this->actingAsAdmin(['regions.index']);

        $this->getJson(route('api.v1.admin.regions.index', ['city_id' => $city->id]))
            ->assertOk()
            ->assertJsonCount(2, 'data.data');
    }

    #[Test]
    public function admin_creates_a_region(): void
    {
        $city = City::factory()->create();
        $this->actingAsAdmin(['regions.store']);

        $this->postJson(route('api.v1.admin.regions.store'), [
            'name' => ['en' => 'Nasr City', 'ar' => 'مدينة نصر'],
            'city_id' => $city->id,
            'active' => true,
        ])->assertCreated()
            ->assertJsonPath('data.region.name', 'Nasr City')
            ->assertJsonPath('data.region.city.id', $city->id);

        $this->assertDatabaseCount('regions', 1);
    }

    #[Test]
    public function creating_a_region_validates_the_payload(): void
    {
        $this->actingAsAdmin(['regions.store']);

        $this->postJson(route('api.v1.admin.regions.store'), ['city_id' => 999])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'city_id', 'active']);
    }

    #[Test]
    public function admin_shows_a_region(): void
    {
        $region = Region::factory()->create();
        $this->actingAsAdmin(['regions.show']);

        $this->getJson(route('api.v1.admin.regions.show', $region))
            ->assertOk()
            ->assertJsonPath('data.region.id', $region->id);
    }

    #[Test]
    public function admin_updates_a_region(): void
    {
        $region = Region::factory()->create();
        $this->actingAsAdmin(['regions.update']);

        $this->putJson(route('api.v1.admin.regions.update', $region), [
            'name' => ['en' => 'Maadi', 'ar' => 'المعادي'],
            'city_id' => $region->city_id,
            'active' => true,
        ])->assertOk()->assertJsonPath('data.region.name', 'Maadi');
    }

    #[Test]
    public function admin_toggles_a_region_status(): void
    {
        $region = Region::factory()->create();
        $this->actingAsAdmin(['regions.status']);

        $this->patchJson(route('api.v1.admin.regions.status', $region))
            ->assertOk()
            ->assertJsonPath('data.region.active', 0);

        $this->assertSame(0, (int) $region->fresh()->active);
    }

    #[Test]
    public function admin_deletes_a_region(): void
    {
        $region = Region::factory()->create();
        $this->actingAsAdmin(['regions.destroy']);

        $this->deleteJson(route('api.v1.admin.regions.destroy', $region))->assertOk();

        $this->assertModelMissing($region);
    }

    #[Test]
    public function showing_a_missing_region_returns_not_found(): void
    {
        $this->actingAsAdmin(['regions.show']);

        $this->getJson(route('api.v1.admin.regions.show', 999))->assertNotFound();
    }
}

<?php

namespace Tests\Feature\Api\V1\Admin;

use App\Enums\TokenAbilityEnum;
use App\Models\CharityCase;
use App\Models\City;
use App\Models\Region;
use App\Models\User;
use Database\Seeders\LaratrustSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CharityCaseTest extends TestCase
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

    /**
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'full_name' => 'Mona Ahmed Hassan',
            'national_id' => '29001011234567',
            'phone' => '01012345678',
            'volunteer_id' => User::factory()->volunteer()->create()->id,
        ], $overrides);
    }

    #[Test]
    public function admin_without_the_permission_is_rejected(): void
    {
        $this->actingAsAdmin(['case_assistances.index']);

        $this->getJson(route('api.v1.admin.charity_cases.index'))->assertForbidden();
    }

    #[Test]
    public function admin_lists_cases_with_their_volunteer(): void
    {
        CharityCase::factory()->count(2)->create();
        $this->actingAsAdmin(['charity_cases.index']);

        $this->getJson(route('api.v1.admin.charity_cases.index'))
            ->assertOk()
            ->assertJsonCount(2, 'data.data')
            ->assertJsonStructure(['data' => ['data' => [['id', 'full_name', 'national_id', 'phone', 'volunteer' => ['id', 'full_name']]], 'permissions']]);
    }

    #[Test]
    public function admin_creates_a_case(): void
    {
        $this->actingAsAdmin(['charity_cases.store']);

        $this->postJson(route('api.v1.admin.charity_cases.store'), $this->payload())
            ->assertCreated()
            ->assertJsonPath('data.case.national_id', '29001011234567');
    }

    #[Test]
    public function a_duplicate_national_id_is_a_validation_error_but_a_shared_phone_is_not(): void
    {
        CharityCase::factory()->create(['national_id' => '29001011234567', 'phone' => '01012345678']);
        $this->actingAsAdmin(['charity_cases.store']);

        $this->postJson(route('api.v1.admin.charity_cases.store'), $this->payload())
            ->assertUnprocessable()
            ->assertJsonValidationErrors('national_id');

        $this->postJson(route('api.v1.admin.charity_cases.store'), $this->payload(['national_id' => '29505051234567']))
            ->assertCreated();
    }

    #[Test]
    public function admin_creates_a_case_without_a_volunteer_or_an_address(): void
    {
        $this->actingAsAdmin(['charity_cases.store']);

        $this->postJson(route('api.v1.admin.charity_cases.store'), $this->payload(['volunteer_id' => null]))
            ->assertCreated()
            ->assertJsonPath('data.case.volunteer_id', null)
            ->assertJsonPath('data.case.volunteer', null)
            ->assertJsonPath('data.case.address', null);

        $this->assertDatabaseCount('addresses', 0);
    }

    #[Test]
    public function admin_creates_a_case_with_a_full_address(): void
    {
        $region = Region::factory()->create();
        $this->actingAsAdmin(['charity_cases.store']);

        $response = $this->postJson(route('api.v1.admin.charity_cases.store'), $this->payload([
            'address' => [
                'city_id' => $region->city_id,
                'region_id' => $region->id,
                'address' => '12 Nile Street',
                'latitude' => 30.0444,
                'longitude' => 31.2357,
            ],
        ]))
            ->assertCreated()
            ->assertJsonPath('data.case.address.city_id', $region->city_id)
            ->assertJsonPath('data.case.address.region_id', $region->id)
            ->assertJsonPath('data.case.address.address', '12 Nile Street')
            ->assertJsonPath('data.case.address.latitude', 30.0444)
            ->assertJsonPath('data.case.address.longitude', 31.2357);

        $this->assertDatabaseHas('addresses', [
            'model_type' => (new CharityCase)->getMorphClass(),
            'model_id' => $response->json('data.case.id'),
            'city_id' => $region->city_id,
        ]);
    }

    #[Test]
    public function an_address_needs_its_city_as_soon_as_any_part_is_sent(): void
    {
        $region = Region::factory()->create();
        $this->actingAsAdmin(['charity_cases.store']);

        foreach (['region_id' => $region->id, 'address' => '12 Nile Street', 'latitude' => 30.1, 'longitude' => 31.2] as $field => $value) {
            $this->postJson(route('api.v1.admin.charity_cases.store'), $this->payload(['address' => [$field => $value]]))
                ->assertUnprocessable()
                ->assertJsonValidationErrors('address.city_id');
        }

        $this->assertDatabaseCount('charity_cases', 0);
    }

    #[Test]
    public function the_city_must_be_an_active_existing_city(): void
    {
        $this->actingAsAdmin(['charity_cases.store']);

        foreach ([City::factory()->inactive()->create()->id, 999999] as $cityId) {
            $this->postJson(route('api.v1.admin.charity_cases.store'), $this->payload(['address' => ['city_id' => $cityId]]))
                ->assertUnprocessable()
                ->assertJsonValidationErrors('address.city_id');
        }
    }

    #[Test]
    public function the_region_must_be_an_active_region_of_the_chosen_city(): void
    {
        $city = City::factory()->create();
        $otherCityRegion = Region::factory()->create();
        $inactiveRegion = Region::factory()->inactive()->create(['city_id' => $city->id]);
        $this->actingAsAdmin(['charity_cases.store']);

        foreach ([$otherCityRegion->id, $inactiveRegion->id] as $regionId) {
            $this->postJson(route('api.v1.admin.charity_cases.store'), $this->payload(['address' => ['city_id' => $city->id, 'region_id' => $regionId]]))
                ->assertUnprocessable()
                ->assertJsonValidationErrors('address.region_id');
        }
    }

    #[Test]
    public function the_map_point_is_a_valid_coordinate_pair(): void
    {
        $city = City::factory()->create();
        $this->actingAsAdmin(['charity_cases.store']);

        $invalid = [
            'address.latitude' => [['latitude' => 91, 'longitude' => 31.2], ['latitude' => 'north', 'longitude' => 31.2], ['latitude' => -90.5, 'longitude' => 31.2]],
            'address.longitude' => [['latitude' => 30.1, 'longitude' => 181], ['latitude' => 30.1], ['latitude' => 30.1, 'longitude' => 'east']],
        ];

        foreach ($invalid as $errorField => $points) {
            foreach ($points as $point) {
                $this->postJson(route('api.v1.admin.charity_cases.store'), $this->payload(['address' => ['city_id' => $city->id] + $point]))
                    ->assertUnprocessable()
                    ->assertJsonValidationErrors($errorField);
            }
        }

        $this->postJson(route('api.v1.admin.charity_cases.store'), $this->payload(['address' => ['city_id' => $city->id, 'longitude' => 31.2]]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('address.latitude');

        $this->postJson(route('api.v1.admin.charity_cases.store'), $this->payload(['address' => ['city_id' => $city->id, 'latitude' => 0, 'longitude' => 0]]))
            ->assertCreated();
    }

    #[Test]
    public function the_volunteer_when_sent_must_still_be_an_active_volunteer(): void
    {
        $this->actingAsAdmin(['charity_cases.store']);

        $this->postJson(route('api.v1.admin.charity_cases.store'), $this->payload(['volunteer_id' => User::factory()->volunteer()->inactive()->create()->id]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('volunteer_id');
    }

    #[Test]
    public function updating_replaces_keeps_or_removes_the_address(): void
    {
        $oldCity = City::factory()->create();
        $newCity = City::factory()->create();
        $charityCase = CharityCase::factory()->withAddress(['city_id' => $oldCity->id])->create(['national_id' => '29001011234567']);
        $this->actingAsAdmin(['charity_cases.update']);

        $this->putJson(route('api.v1.admin.charity_cases.update', $charityCase), $this->payload(['address' => ['city_id' => $newCity->id, 'address' => 'New street']]))
            ->assertOk()
            ->assertJsonPath('data.case.address.city_id', $newCity->id);
        $this->assertDatabaseCount('addresses', 1);

        $this->putJson(route('api.v1.admin.charity_cases.update', $charityCase), $this->payload(['full_name' => 'Renamed Person']))->assertOk();
        $this->assertSame($newCity->id, $charityCase->fresh()->address->city_id);
        $this->assertNull($charityCase->fresh()->address->region_id);

        $this->putJson(route('api.v1.admin.charity_cases.update', $charityCase), $this->payload(['address' => null]))
            ->assertOk()
            ->assertJsonPath('data.case.address', null);
        $this->assertDatabaseCount('addresses', 0);
    }

    #[Test]
    public function show_and_list_include_the_address(): void
    {
        $charityCase = CharityCase::factory()->unassigned()->withAddress(['address' => '12 Nile Street'])->create();
        $this->actingAsAdmin(['charity_cases.show', 'charity_cases.index']);

        $this->getJson(route('api.v1.admin.charity_cases.show', $charityCase))
            ->assertOk()
            ->assertJsonPath('data.case.address.address', '12 Nile Street')
            ->assertJsonStructure(['data' => ['case' => ['address' => ['city' => ['id', 'name']]]]]);

        $this->getJson(route('api.v1.admin.charity_cases.index'))
            ->assertOk()
            ->assertJsonPath('data.data.0.address.address', '12 Nile Street')
            ->assertJsonPath('data.data.0.volunteer', null);
    }

    #[Test]
    public function form_options_list_only_active_volunteers(): void
    {
        User::factory()->volunteer()->create();
        User::factory()->volunteer()->inactive()->create();
        $this->actingAsAdmin(['charity_cases.store']);

        $this->getJson(route('api.v1.admin.charity_cases.form-options'))
            ->assertOk()
            ->assertJsonMissingPath('data.categories')
            ->assertJsonCount(1, 'data.volunteers');
    }

    #[Test]
    public function admin_filters_the_list_by_city_and_region(): void
    {
        $region = Region::factory()->create();
        $inRegion = CharityCase::factory()->withAddress(['city_id' => $region->city_id, 'region_id' => $region->id])->create();
        CharityCase::factory()->withAddress(['city_id' => $region->city_id])->create();
        CharityCase::factory()->withAddress()->create();
        CharityCase::factory()->create();
        $this->actingAsAdmin(['charity_cases.index']);

        $this->getJson(route('api.v1.admin.charity_cases.index', ['city_id' => $region->city_id]))
            ->assertOk()
            ->assertJsonCount(2, 'data.data');

        $this->getJson(route('api.v1.admin.charity_cases.index', ['city_id' => $region->city_id, 'region_id' => $region->id]))
            ->assertOk()
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath('data.data.0.id', $inRegion->id);
    }

    #[Test]
    public function filter_options_list_the_active_volunteers_cities_and_regions_for_the_list_permission(): void
    {
        $region = Region::factory()->create();
        User::factory()->volunteer()->create();
        User::factory()->volunteer()->inactive()->create();
        $this->actingAsAdmin(['charity_cases.index']);

        $this->getJson(route('api.v1.admin.charity_cases.filter-options'))
            ->assertOk()
            ->assertJsonCount(1, 'data.volunteers')
            ->assertJsonPath('data.cities.0.value', $region->city_id)
            ->assertJsonPath('data.regions.0.value', $region->id)
            ->assertJsonPath('data.regions.0.city_id', $region->city_id);
    }

    #[Test]
    public function filter_options_need_the_list_permission(): void
    {
        $this->actingAsAdmin(['charity_cases.store']);

        $this->getJson(route('api.v1.admin.charity_cases.filter-options'))->assertForbidden();
    }

    #[Test]
    public function form_options_list_the_active_cities_and_regions_with_their_city(): void
    {
        $region = Region::factory()->create();
        Region::factory()->inactive()->create(['city_id' => $region->city_id]);
        City::factory()->inactive()->create();
        $this->actingAsAdmin(['charity_cases.store']);

        $this->getJson(route('api.v1.admin.charity_cases.form-options'))
            ->assertOk()
            ->assertJsonCount(1, 'data.cities')
            ->assertJsonPath('data.cities.0.value', $region->city_id)
            ->assertJsonCount(1, 'data.regions')
            ->assertJsonPath('data.regions.0.value', $region->id)
            ->assertJsonPath('data.regions.0.city_id', $region->city_id);
    }

    #[Test]
    public function show_includes_the_cases_assistances(): void
    {
        $charityCase = CharityCase::factory()->create();
        $this->actingAsAdmin(['charity_cases.show']);

        $this->getJson(route('api.v1.admin.charity_cases.show', $charityCase))
            ->assertOk()
            ->assertJsonPath('data.case.id', $charityCase->id)
            ->assertJsonPath('data.case.assistances', []);
    }
}

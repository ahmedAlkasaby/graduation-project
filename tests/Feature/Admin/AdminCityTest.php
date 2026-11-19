<?php

namespace Tests\Feature\Admin;

use App\Models\City;
use App\Models\Country;
use App\Models\User;
use Database\Seeders\LaratrustSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminCityTest extends TestCase
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
    private function signInAdmin(array $permissions): User
    {
        $admin = User::factory()->admin()->create();
        $admin->givePermissions($permissions);
        $this->actingAs($admin, 'web');

        return $admin;
    }

    #[Test]
    public function list_shows_active_and_inactive_cities_and_filters_them(): void
    {
        $active = City::factory()->create(['name' => ['en' => 'Cairo', 'ar' => 'القاهرة']]);
        $inactive = City::factory()->inactive()->create(['name' => ['en' => 'Giza', 'ar' => 'الجيزة']]);
        $this->signInAdmin(['cities.index']);

        $this->get(route('admin.cities.index'))->assertOk()->assertSee('Cairo')->assertSee('Giza');

        $this->get(route('admin.cities.index', ['active' => 0]))
            ->assertOk()
            ->assertDontSee($active->nameLang('en'))
            ->assertSee($inactive->nameLang('en'));
    }

    #[Test]
    public function create_edit_and_show_screens_render(): void
    {
        $city = City::factory()->create();
        $this->signInAdmin(['cities.show', 'cities.store', 'cities.update']);

        $this->get(route('admin.cities.create'))->assertOk();
        $this->get(route('admin.cities.edit', $city))->assertOk()->assertSee($city->nameLang('en'));
        $this->get(route('admin.cities.show', $city))->assertOk()->assertSee($city->nameLang('en'));
    }

    #[Test]
    public function store_creates_a_city(): void
    {
        $country = Country::factory()->create();
        $this->signInAdmin(['cities.store']);

        $this->post(route('admin.cities.store'), [
            'name' => ['en' => 'Luxor', 'ar' => 'الأقصر'],
            'country_id' => $country->id,
            'active' => 1,
        ])->assertRedirect(route('admin.cities.index'))->assertSessionHas('success');

        $this->assertDatabaseHas('cities', ['country_id' => $country->id, 'name->en' => 'Luxor']);
    }

    #[Test]
    public function store_rejects_a_missing_name(): void
    {
        $country = Country::factory()->create();
        $this->signInAdmin(['cities.store']);

        $this->from(route('admin.cities.create'))
            ->post(route('admin.cities.store'), ['country_id' => $country->id, 'active' => 1])
            ->assertRedirect(route('admin.cities.create'))
            ->assertSessionHasErrors(['name.en', 'name.ar']);
    }

    #[Test]
    public function update_saves_the_changes(): void
    {
        $city = City::factory()->create();
        $this->signInAdmin(['cities.update']);

        $this->patch(route('admin.cities.update', $city), [
            'name' => ['en' => 'Aswan', 'ar' => 'أسوان'],
            'country_id' => $city->country_id,
            'active' => 0,
        ])->assertRedirect(route('admin.cities.index'));

        $this->assertSame('Aswan', $city->fresh()->nameLang('en'));
        $this->assertFalse((bool) $city->fresh()->active);
    }

    #[Test]
    public function status_flips_the_city(): void
    {
        $city = City::factory()->create();
        $this->signInAdmin(['cities.status']);

        $this->patchJson(route('admin.cities.status', $city))
            ->assertOk()
            ->assertJson(['success' => true, 'active' => false]);

        $this->assertFalse((bool) $city->fresh()->active);
    }

    #[Test]
    public function destroy_deletes_the_city(): void
    {
        $city = City::factory()->create();
        $this->signInAdmin(['cities.destroy']);

        $this->delete(route('admin.cities.destroy', $city))->assertRedirect(route('admin.cities.index'));

        $this->assertModelMissing($city);
    }

    #[Test]
    public function destroy_needs_its_permission(): void
    {
        $city = City::factory()->create();
        $this->signInAdmin(['cities.index']);

        $this->delete(route('admin.cities.destroy', $city))->assertRedirect(route('admin.unauthorized'));

        $this->assertModelExists($city);
    }
}

<?php

namespace Tests\Feature\Admin;

use App\Models\City;
use App\Models\Region;
use App\Models\User;
use Database\Seeders\LaratrustSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminRegionTest extends TestCase
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
    public function list_filters_regions_by_city(): void
    {
        $nasrCity = Region::factory()->create(['name' => ['en' => 'Nasr City', 'ar' => 'مدينة نصر']]);
        $dokki = Region::factory()->create(['name' => ['en' => 'Dokki', 'ar' => 'الدقي']]);
        $this->signInAdmin(['regions.index']);

        $this->get(route('admin.regions.index', ['city_id' => $nasrCity->city_id]))
            ->assertOk()
            ->assertSee('Nasr City')
            ->assertDontSee($dokki->nameLang('en'));
    }

    #[Test]
    public function create_edit_and_show_screens_render(): void
    {
        $region = Region::factory()->create();
        $this->signInAdmin(['regions.show', 'regions.store', 'regions.update']);

        $this->get(route('admin.regions.create'))->assertOk();
        $this->get(route('admin.regions.edit', $region))->assertOk()->assertSee($region->nameLang('en'));
        $this->get(route('admin.regions.show', $region))->assertOk()->assertSee($region->nameLang('en'));
    }

    #[Test]
    public function store_creates_a_region(): void
    {
        $city = City::factory()->create();
        $this->signInAdmin(['regions.store']);

        $this->post(route('admin.regions.store'), [
            'name' => ['en' => 'Maadi', 'ar' => 'المعادي'],
            'city_id' => $city->id,
            'active' => 1,
        ])->assertRedirect(route('admin.regions.index'))->assertSessionHas('success');

        $this->assertDatabaseHas('regions', ['city_id' => $city->id, 'name->en' => 'Maadi']);
    }

    #[Test]
    public function store_rejects_an_unknown_city(): void
    {
        $this->signInAdmin(['regions.store']);

        $this->post(route('admin.regions.store'), [
            'name' => ['en' => 'Maadi', 'ar' => 'المعادي'],
            'city_id' => 999,
            'active' => 1,
        ])->assertSessionHasErrors('city_id');
    }

    #[Test]
    public function status_flips_and_destroy_deletes_the_region(): void
    {
        $region = Region::factory()->create();
        $this->signInAdmin(['regions.status', 'regions.destroy']);

        $this->patchJson(route('admin.regions.status', $region))->assertOk()->assertJson(['active' => false]);
        $this->delete(route('admin.regions.destroy', $region))->assertRedirect(route('admin.regions.index'));

        $this->assertModelMissing($region);
    }
}

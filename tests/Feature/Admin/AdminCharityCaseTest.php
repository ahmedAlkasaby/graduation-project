<?php

namespace Tests\Feature\Admin;

use App\Models\CharityCase;
use App\Models\City;
use App\Models\Region;
use App\Models\User;
use Database\Seeders\LaratrustSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminCharityCaseTest extends TestCase
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
    public function list_searches_by_national_id(): void
    {
        CharityCase::factory()->create(['full_name' => 'Mona Ahmed', 'national_id' => '29001011234567']);
        CharityCase::factory()->create(['full_name' => 'Karim Adel']);
        $this->signInAdmin(['charity_cases.index']);

        $this->get(route('admin.charity_cases.index', ['search' => '29001011234567']))
            ->assertOk()
            ->assertSee('Mona Ahmed')
            ->assertDontSee('Karim Adel');
    }

    #[Test]
    public function list_filters_by_the_city_and_region_of_the_address(): void
    {
        $region = Region::factory()->create();
        CharityCase::factory()->withAddress(['city_id' => $region->city_id, 'region_id' => $region->id])->create(['full_name' => 'Mona Ahmed']);
        CharityCase::factory()->withAddress(['city_id' => $region->city_id])->create(['full_name' => 'Karim Adel']);
        CharityCase::factory()->withAddress()->create(['full_name' => 'Other City Person']);
        CharityCase::factory()->create(['full_name' => 'No Address Person']);
        $this->signInAdmin(['charity_cases.index']);

        $this->get(route('admin.charity_cases.index', ['city_id' => $region->city_id]))
            ->assertOk()
            ->assertSee('Mona Ahmed')
            ->assertSee('Karim Adel')
            ->assertDontSee('Other City Person')
            ->assertDontSee('No Address Person');

        $this->get(route('admin.charity_cases.index', ['city_id' => $region->city_id, 'region_id' => $region->id]))
            ->assertOk()
            ->assertSee('Mona Ahmed')
            ->assertDontSee('Karim Adel');
    }

    #[Test]
    public function create_edit_and_show_screens_render(): void
    {
        $charityCase = CharityCase::factory()->create();
        $this->signInAdmin(['charity_cases.show', 'charity_cases.store', 'charity_cases.update']);

        $this->get(route('admin.charity_cases.create'))->assertOk();
        $this->get(route('admin.charity_cases.edit', $charityCase))->assertOk()->assertSee($charityCase->full_name);
        $this->get(route('admin.charity_cases.show', $charityCase))->assertOk()->assertSee($charityCase->full_name);
    }

    #[Test]
    public function store_creates_a_case_and_opens_it(): void
    {
        $this->signInAdmin(['charity_cases.store']);

        $response = $this->post(route('admin.charity_cases.store'), $this->payload());

        $charityCase = CharityCase::firstWhere('national_id', '29001011234567');
        $this->assertNotNull($charityCase);
        $response->assertRedirect(route('admin.charity_cases.show', $charityCase))->assertSessionHas('success');
    }

    #[Test]
    public function only_the_name_national_id_and_phone_are_required(): void
    {
        $this->signInAdmin(['charity_cases.store']);

        $this->post(route('admin.charity_cases.store'), [])
            ->assertSessionHasErrors(['full_name', 'national_id', 'phone'])
            ->assertSessionDoesntHaveErrors(['volunteer_id', 'address.city_id']);

        $this->assertSame(0, CharityCase::count());
    }

    #[Test]
    public function a_case_is_saved_without_a_volunteer_and_without_an_address(): void
    {
        $this->signInAdmin(['charity_cases.store']);

        $this->post(route('admin.charity_cases.store'), $this->payload(['volunteer_id' => null]))->assertSessionHasNoErrors();

        $charityCase = CharityCase::firstWhere('national_id', '29001011234567');
        $this->assertNull($charityCase->volunteer_id);
        $this->assertNull($charityCase->address);
    }

    #[Test]
    public function the_untouched_address_fields_of_the_form_save_no_address(): void
    {
        $this->signInAdmin(['charity_cases.store']);

        $emptyAddress = ['city_id' => '', 'region_id' => '', 'address' => '', 'latitude' => '', 'longitude' => ''];

        $this->post(route('admin.charity_cases.store'), $this->payload(['address' => $emptyAddress]))->assertSessionHasNoErrors();

        $this->assertDatabaseCount('addresses', 0);
    }

    #[Test]
    public function an_address_with_only_the_city_is_saved(): void
    {
        $city = City::factory()->create();
        $this->signInAdmin(['charity_cases.store']);

        $this->post(route('admin.charity_cases.store'), $this->payload(['address' => ['city_id' => $city->id, 'region_id' => '']]))
            ->assertSessionHasNoErrors();

        $address = CharityCase::firstWhere('national_id', '29001011234567')->address;
        $this->assertSame($city->id, $address->city_id);
        $this->assertNull($address->region_id);
        $this->assertNull($address->latitude);
    }

    #[Test]
    public function an_address_without_its_city_is_rejected(): void
    {
        $this->signInAdmin(['charity_cases.store']);

        $this->post(route('admin.charity_cases.store'), $this->payload(['address' => ['address' => '12 Nile St', 'city_id' => '']]))
            ->assertSessionHasErrors('address.city_id');

        $this->assertSame(0, CharityCase::count());
    }

    #[Test]
    public function create_edit_and_show_screens_render_the_address(): void
    {
        $charityCase = CharityCase::factory()->withAddress(['address' => '12 Nile Street'])->create();
        $this->signInAdmin(['charity_cases.show', 'charity_cases.store', 'charity_cases.update']);

        $this->get(route('admin.charity_cases.create'))->assertOk()->assertSee('address[city_id]', false);
        $this->get(route('admin.charity_cases.edit', $charityCase))->assertOk()->assertSee('12 Nile Street');
        $this->get(route('admin.charity_cases.show', $charityCase))->assertOk()->assertSee('12 Nile Street');
    }

    #[Test]
    public function clearing_the_city_on_the_edit_form_removes_the_address(): void
    {
        $charityCase = CharityCase::factory()->withAddress()->create();
        $this->signInAdmin(['charity_cases.update']);

        $emptyAddress = ['city_id' => '', 'region_id' => '', 'address' => '', 'latitude' => '', 'longitude' => ''];

        $this->put(route('admin.charity_cases.update', $charityCase), $this->payload(['address' => $emptyAddress]))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('addresses', 0);
    }

    #[Test]
    public function a_duplicate_national_id_is_rejected(): void
    {
        CharityCase::factory()->create(['national_id' => '29001011234567']);
        $this->signInAdmin(['charity_cases.store']);

        $this->post(route('admin.charity_cases.store'), $this->payload())->assertSessionHasErrors('national_id');

        $this->assertSame(1, CharityCase::where('national_id', '29001011234567')->count());
    }

    #[Test]
    public function a_national_id_that_is_not_14_egyptian_digits_is_rejected(): void
    {
        $this->signInAdmin(['charity_cases.store']);

        $this->post(route('admin.charity_cases.store'), $this->payload(['national_id' => '2900101123456']))->assertSessionHasErrors('national_id');
        $this->post(route('admin.charity_cases.store'), $this->payload(['national_id' => '19001011234567']))->assertSessionHasErrors('national_id');
    }

    #[Test]
    public function a_shared_phone_is_allowed(): void
    {
        CharityCase::factory()->create(['phone' => '01012345678']);
        $this->signInAdmin(['charity_cases.store']);

        $this->post(route('admin.charity_cases.store'), $this->payload())->assertSessionHasNoErrors();

        $this->assertSame(2, CharityCase::where('phone', '01012345678')->count());
    }

    #[Test]
    public function the_volunteer_must_be_an_active_volunteer_user(): void
    {
        $admin = $this->signInAdmin(['charity_cases.store']);
        $inactiveVolunteer = User::factory()->volunteer()->inactive()->create();
        $volunteer = User::factory()->volunteer()->create();

        $this->post(route('admin.charity_cases.store'), $this->payload(['volunteer_id' => $admin->id]))->assertSessionHasErrors('volunteer_id');
        $this->post(route('admin.charity_cases.store'), $this->payload(['volunteer_id' => $inactiveVolunteer->id]))->assertSessionHasErrors('volunteer_id');
        $this->post(route('admin.charity_cases.store'), $this->payload(['volunteer_id' => $volunteer->id]))->assertSessionHasNoErrors();
    }

    #[Test]
    public function update_keeps_its_own_national_id(): void
    {
        $charityCase = CharityCase::factory()->create(['national_id' => '29001011234567']);
        $this->signInAdmin(['charity_cases.update']);

        $this->put(route('admin.charity_cases.update', $charityCase), $this->payload(['full_name' => 'Renamed Person']))
            ->assertRedirect(route('admin.charity_cases.show', $charityCase))
            ->assertSessionHasNoErrors();

        $this->assertSame('Renamed Person', $charityCase->fresh()->full_name);
    }

    #[Test]
    public function quick_create_answers_with_the_new_case_as_json(): void
    {
        $this->signInAdmin(['charity_cases.store']);

        $this->postJson(route('admin.charity_cases.store'), $this->payload())
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('case.id', CharityCase::first()->id);
    }

    #[Test]
    public function the_full_form_returns_to_the_assistance_form_with_the_case_selected(): void
    {
        $this->signInAdmin(['charity_cases.store']);

        $response = $this->post(route('admin.charity_cases.store'), $this->payload(['return_to' => 'assistance']));

        $response->assertRedirect(route('admin.case_assistances.create', ['case_id' => CharityCase::first()->id]));
    }

    #[Test]
    public function lookup_finds_a_case_by_phone_for_the_assistance_form(): void
    {
        $match = CharityCase::factory()->create(['phone' => '01099998888']);
        CharityCase::factory()->create(['phone' => '01011112222']);
        $this->signInAdmin(['case_assistances.store']);

        $this->getJson(route('admin.charity_cases.lookup', ['search' => '01099998888']))
            ->assertOk()
            ->assertJsonCount(1, 'results')
            ->assertJsonPath('results.0.id', $match->id);
    }

    #[Test]
    public function an_admin_without_the_permission_is_sent_to_the_unauthorized_page(): void
    {
        $this->signInAdmin(['case_assistances.index']);

        $this->get(route('admin.charity_cases.index'))->assertRedirect(route('admin.unauthorized'));
        $this->post(route('admin.charity_cases.store'), $this->payload())->assertRedirect(route('admin.unauthorized'));
    }
}

<?php

namespace Tests\Feature\Admin;

use App\Models\CharityCase;
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
    public function every_field_is_required(): void
    {
        $this->signInAdmin(['charity_cases.store']);

        $this->post(route('admin.charity_cases.store'), [])
            ->assertSessionHasErrors(['full_name', 'national_id', 'phone', 'volunteer_id']);

        $this->assertSame(0, CharityCase::count());
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

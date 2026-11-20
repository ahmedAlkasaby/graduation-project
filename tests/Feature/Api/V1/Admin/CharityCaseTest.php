<?php

namespace Tests\Feature\Api\V1\Admin;

use App\Enums\TokenAbilityEnum;
use App\Models\CaseCategory;
use App\Models\CharityCase;
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
            'category_id' => CaseCategory::factory()->create()->id,
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
    public function admin_lists_cases_with_their_category(): void
    {
        CharityCase::factory()->count(2)->create();
        $this->actingAsAdmin(['charity_cases.index']);

        $this->getJson(route('api.v1.admin.charity_cases.index'))
            ->assertOk()
            ->assertJsonCount(2, 'data.data')
            ->assertJsonStructure(['data' => ['data' => [['id', 'full_name', 'national_id', 'phone', 'category' => ['id', 'name']]], 'permissions']]);
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
    public function form_options_list_categories_and_active_volunteers(): void
    {
        CaseCategory::factory()->create();
        User::factory()->volunteer()->create();
        User::factory()->volunteer()->inactive()->create();
        $this->actingAsAdmin(['charity_cases.store']);

        $this->getJson(route('api.v1.admin.charity_cases.form-options'))
            ->assertOk()
            ->assertJsonCount(1, 'data.categories')
            ->assertJsonCount(1, 'data.volunteers');
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

<?php

namespace Tests\Feature\Api\V1\Admin;

use App\Enums\AssistanceTypeEnum;
use App\Enums\TokenAbilityEnum;
use App\Models\CaseAssistance;
use App\Models\CharityCase;
use App\Models\User;
use Database\Seeders\LaratrustSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CaseAssistanceTest extends TestCase
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

    private function createRecurring(): CaseAssistance
    {
        $this->postJson(route('api.v1.admin.case_assistances.store'), [
            'case_id' => CharityCase::factory()->create()->id,
            'assistance_type' => AssistanceTypeEnum::Recurring->value,
            'title' => ['en' => 'Monthly support', 'ar' => 'دعم شهري'],
            'amount_per_period' => '1000',
            'start_month' => '2027-01',
        ])->assertCreated();

        return CaseAssistance::latest('id')->firstOrFail();
    }

    #[Test]
    public function admin_without_the_permission_is_rejected(): void
    {
        $this->actingAsAdmin(['case_assistances.index']);

        $this->postJson(route('api.v1.admin.case_assistances.store'), [])->assertForbidden();
    }

    #[Test]
    public function creating_a_recurring_assistance_returns_its_twelve_installments(): void
    {
        $this->actingAsAdmin(['case_assistances.store']);

        $this->postJson(route('api.v1.admin.case_assistances.store'), [
            'case_id' => CharityCase::factory()->create()->id,
            'assistance_type' => AssistanceTypeEnum::Recurring->value,
            'title' => ['en' => 'Monthly support', 'ar' => 'دعم شهري'],
            'amount_per_period' => '1000',
            'start_month' => '2027-01',
        ])
            ->assertCreated()
            ->assertJsonPath('data.assistance.amount_per_period', '1000.00')
            ->assertJsonPath('data.assistance.requested_amount', null)
            ->assertJsonPath('data.assistance.currency', 'EGP')
            ->assertJsonCount(12, 'data.assistance.installments')
            ->assertJsonPath('data.assistance.installments.0.due_at', '2027-01-01')
            ->assertJsonPath('data.assistance.installments.11.due_at', '2027-12-01')
            ->assertJsonPath('data.assistance.installments.0.paid_at', null);
    }

    #[Test]
    public function sending_both_amounts_is_a_validation_error(): void
    {
        $this->actingAsAdmin(['case_assistances.store']);

        $this->postJson(route('api.v1.admin.case_assistances.store'), [
            'case_id' => CharityCase::factory()->create()->id,
            'assistance_type' => AssistanceTypeEnum::OneTime->value,
            'title' => ['en' => 'Surgery', 'ar' => 'عملية'],
            'requested_amount' => '5000',
            'amount_per_period' => '500',
            'due_at' => '2026-12-01',
        ])->assertUnprocessable()->assertJsonValidationErrors('amount_per_period');
    }

    #[Test]
    public function update_changes_the_title_but_rejects_financial_fields(): void
    {
        $this->actingAsAdmin(['case_assistances.store', 'case_assistances.update']);
        $assistance = $this->createRecurring();
        $title = ['en' => 'Renamed', 'ar' => 'اسم جديد'];

        $this->putJson(route('api.v1.admin.case_assistances.update', $assistance), ['title' => $title, 'amount_per_period' => '5'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('amount_per_period');
        $this->putJson(route('api.v1.admin.case_assistances.update', $assistance), ['title' => $title, 'assistance_type' => 'one_time', 'requested_amount' => '5'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['assistance_type', 'requested_amount']);

        $this->putJson(route('api.v1.admin.case_assistances.update', $assistance), ['title' => $title])
            ->assertOk()
            ->assertJsonPath('data.assistance.title_translations.en', 'Renamed')
            ->assertJsonPath('data.assistance.amount_per_period', '1000.00')
            ->assertJsonCount(12, 'data.assistance.installments');
    }

    #[Test]
    public function a_pending_installment_is_cancelled_once(): void
    {
        $this->actingAsAdmin(['case_assistances.store', 'case_assistances.cancel_installment']);
        $assistance = $this->createRecurring();
        $installment = $assistance->installments()->orderBy('due_at')->first();
        $url = route('api.v1.admin.case_assistances.cancel_installment', [$assistance, $installment]);

        $this->patchJson($url, ['notes' => 'Not needed this month'])
            ->assertOk()
            ->assertJsonPath('data.installment.status', 'cancelled')
            ->assertJsonPath('data.installment.paid_at', null)
            ->assertJsonPath('data.installment.notes', 'Not needed this month');

        $this->patchJson($url)->assertUnprocessable()->assertJsonValidationErrors('installment');
    }

    #[Test]
    public function index_filters_by_type(): void
    {
        CaseAssistance::factory()->create();
        CaseAssistance::factory()->recurring()->create();
        $this->actingAsAdmin(['case_assistances.index']);

        $this->getJson(route('api.v1.admin.case_assistances.index', ['assistance_type' => 'recurring']))
            ->assertOk()
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath('data.data.0.assistance_type', 'recurring');
    }
}

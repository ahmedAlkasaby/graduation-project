<?php

namespace Tests\Feature\Admin;

use App\Enums\AssistanceTypeEnum;
use App\Models\AssistanceInstallment;
use App\Models\CaseAssistance;
use App\Models\CharityCase;
use App\Models\User;
use Database\Seeders\LaratrustSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminCaseAssistanceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

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
        $this->admin = User::factory()->admin()->create();
        $this->admin->givePermissions($permissions);
        $this->actingAs($this->admin, 'web');

        return $this->admin;
    }

    /**
     * @return array<string, mixed>
     */
    private function oneTime(array $overrides = []): array
    {
        return array_merge([
            'case_id' => CharityCase::factory()->create()->id,
            'assistance_type' => AssistanceTypeEnum::OneTime->value,
            'title' => ['en' => 'Surgery', 'ar' => 'عملية جراحية'],
            'requested_amount' => '5000',
            'due_at' => '2026-12-01',
        ], $overrides);
    }

    /**
     * @return array<string, mixed>
     */
    private function recurring(array $overrides = []): array
    {
        return array_merge([
            'case_id' => CharityCase::factory()->create()->id,
            'assistance_type' => AssistanceTypeEnum::Recurring->value,
            'title' => ['en' => 'Monthly support', 'ar' => 'دعم شهري'],
            'amount_per_period' => '1000',
            'start_month' => '2027-01',
        ], $overrides);
    }

    private function createAssistance(array $payload): CaseAssistance
    {
        $this->post(route('admin.case_assistances.store'), $payload)->assertSessionHasNoErrors();

        return CaseAssistance::latest('id')->firstOrFail();
    }

    #[Test]
    public function a_one_time_assistance_gets_one_unpaid_installment_for_its_amount(): void
    {
        $this->signInAdmin(['case_assistances.store']);

        $response = $this->post(route('admin.case_assistances.store'), $this->oneTime());

        $assistance = CaseAssistance::firstOrFail();
        $response->assertRedirect(route('admin.case_assistances.show', $assistance))->assertSessionHas('success');

        $this->assertSame(AssistanceTypeEnum::OneTime, $assistance->assistance_type);
        $this->assertSame('5000.00', $assistance->requested_amount);
        $this->assertNull($assistance->amount_per_period);
        $this->assertSame($this->admin->id, $assistance->created_by);

        $installment = $assistance->installments()->sole();
        $this->assertSame('2026-12-01', $installment->due_at->toDateString());
        $this->assertSame('5000.00', $installment->amount);
        $this->assertNull($installment->paid_at);
        $this->assertNull($installment->cancelled_at);
    }

    #[Test]
    public function a_recurring_assistance_with_an_end_month_gets_one_installment_per_month_inclusive(): void
    {
        $this->signInAdmin(['case_assistances.store']);

        $assistance = $this->createAssistance($this->recurring(['start_month' => '2027-01', 'end_month' => '2027-06']));

        $this->assertNull($assistance->requested_amount);
        $this->assertSame('1000.00', $assistance->amount_per_period);
        $this->assertSame(
            ['2027-01-01', '2027-02-01', '2027-03-01', '2027-04-01', '2027-05-01', '2027-06-01'],
            $assistance->installments()->orderBy('due_at')->get()->map(fn ($installment) => $installment->due_at->toDateString())->all(),
        );
        $this->assertSame(6, $assistance->installments()->where('amount', 1000)->whereNull('paid_at')->whereNull('cancelled_at')->count());
    }

    #[Test]
    public function a_recurring_assistance_without_an_end_month_gets_twelve_installments(): void
    {
        $this->signInAdmin(['case_assistances.store']);

        $assistance = $this->createAssistance($this->recurring(['start_month' => '2027-01']));

        $dueDates = $assistance->installments()->orderBy('due_at')->pluck('due_at');
        $this->assertCount(12, $dueDates);
        $this->assertSame('2027-01-01', $dueDates->first()->toDateString());
        $this->assertSame('2027-12-01', $dueDates->last()->toDateString());
    }

    #[Test]
    public function every_monthly_installment_falls_on_the_first_of_the_month(): void
    {
        $this->signInAdmin(['case_assistances.store']);

        $assistance = $this->createAssistance($this->recurring(['start_month' => '2027-11', 'end_month' => '2028-03']));

        $assistance->installments->each(fn (AssistanceInstallment $installment) => $this->assertSame(1, $installment->due_at->day));
        $this->assertSame(5, $assistance->installments->count());
    }

    #[Test]
    public function the_amount_of_the_other_type_is_prohibited(): void
    {
        $this->signInAdmin(['case_assistances.store']);

        $this->post(route('admin.case_assistances.store'), $this->oneTime(['amount_per_period' => '100']))
            ->assertSessionHasErrors('amount_per_period');
        $this->post(route('admin.case_assistances.store'), $this->recurring(['requested_amount' => '100']))
            ->assertSessionHasErrors('requested_amount');
        $this->post(route('admin.case_assistances.store'), $this->recurring(['due_at' => '2027-01-15']))
            ->assertSessionHasErrors('due_at');

        $this->assertSame(0, CaseAssistance::count());
    }

    #[Test]
    public function each_type_requires_its_own_amount_and_dates(): void
    {
        $this->signInAdmin(['case_assistances.store']);

        $this->post(route('admin.case_assistances.store'), $this->oneTime(['requested_amount' => null, 'due_at' => null]))
            ->assertSessionHasErrors(['requested_amount', 'due_at']);
        $this->post(route('admin.case_assistances.store'), $this->recurring(['amount_per_period' => null, 'start_month' => null]))
            ->assertSessionHasErrors(['amount_per_period', 'start_month']);
    }

    #[Test]
    public function the_end_month_cannot_precede_the_start_or_exceed_the_ceiling(): void
    {
        $this->signInAdmin(['case_assistances.store']);

        $this->post(route('admin.case_assistances.store'), $this->recurring(['start_month' => '2027-06', 'end_month' => '2027-05']))
            ->assertSessionHasErrors('end_month');
        $this->post(route('admin.case_assistances.store'), $this->recurring(['start_month' => '2027-01', 'end_month' => '2037-01']))
            ->assertSessionHasErrors('end_month');

        $this->assertSame(0, AssistanceInstallment::count());
    }

    #[Test]
    public function editing_changes_only_the_title_and_description(): void
    {
        $this->signInAdmin(['case_assistances.store', 'case_assistances.update']);
        $assistance = $this->createAssistance($this->oneTime());

        $this->put(route('admin.case_assistances.update', $assistance), [
            'title' => ['en' => 'Heart surgery', 'ar' => 'عملية قلب'],
            'description' => 'Updated notes',
        ])->assertRedirect(route('admin.case_assistances.show', $assistance))->assertSessionHasNoErrors();

        $assistance->refresh();
        $this->assertSame('Heart surgery', $assistance->titleLang('en'));
        $this->assertSame('Updated notes', $assistance->description);
    }

    #[Test]
    public function editing_cannot_change_the_amounts_type_or_schedule(): void
    {
        $this->signInAdmin(['case_assistances.store', 'case_assistances.update']);
        $oneTime = $this->createAssistance($this->oneTime());
        $recurring = $this->createAssistance($this->recurring());
        $title = ['en' => 'Changed', 'ar' => 'تغيير'];

        $this->put(route('admin.case_assistances.update', $oneTime), ['title' => $title, 'requested_amount' => '9000'])
            ->assertSessionHasErrors('requested_amount');
        $this->put(route('admin.case_assistances.update', $recurring), ['title' => $title, 'amount_per_period' => '2000'])
            ->assertSessionHasErrors('amount_per_period');
        $this->put(route('admin.case_assistances.update', $oneTime), ['title' => $title, 'assistance_type' => AssistanceTypeEnum::Recurring->value])
            ->assertSessionHasErrors('assistance_type');
        $this->put(route('admin.case_assistances.update', $recurring), ['title' => $title, 'start_month' => '2028-01', 'end_month' => '2028-03'])
            ->assertSessionHasErrors(['start_month', 'end_month']);
        $this->put(route('admin.case_assistances.update', $oneTime), [
            'title' => $title,
            'installments' => [['id' => $oneTime->installments()->first()->id, 'amount' => '1', 'due_at' => '2030-01-01']],
        ])->assertSessionHasErrors('installments');

        $this->assertSame('5000.00', $oneTime->fresh()->requested_amount);
        $this->assertSame(AssistanceTypeEnum::OneTime, $oneTime->fresh()->assistance_type);
        $this->assertSame('1000.00', $recurring->fresh()->amount_per_period);
        $this->assertSame('Surgery', $oneTime->fresh()->titleLang('en'));
        $this->assertSame(12, $recurring->installments()->count());
        $installment = $oneTime->installments()->sole();
        $this->assertSame('5000.00', $installment->amount);
        $this->assertSame('2026-12-01', $installment->due_at->toDateString());
    }

    #[Test]
    public function index_create_show_and_edit_screens_render(): void
    {
        $this->signInAdmin(['case_assistances.index', 'case_assistances.show', 'case_assistances.store', 'case_assistances.update', 'charity_cases.store']);
        $assistance = $this->createAssistance($this->recurring());
        $charityCase = $assistance->charityCase;

        $this->get(route('admin.case_assistances.index'))->assertOk()->assertSee('Monthly support');
        $this->get(route('admin.case_assistances.create', ['case_id' => $charityCase->id]))->assertOk()->assertSee($charityCase->full_name);
        $this->get(route('admin.case_assistances.show', $assistance))->assertOk()->assertSee('2027-12-01')->assertSee($charityCase->full_name);
        $this->get(route('admin.case_assistances.edit', $assistance))->assertOk()->assertSee('Monthly support');
    }

    #[Test]
    public function a_pending_installment_can_be_cancelled_with_a_note(): void
    {
        $this->signInAdmin(['case_assistances.store', 'case_assistances.cancel_installment']);
        $assistance = $this->createAssistance($this->recurring());
        $installment = $assistance->installments()->orderBy('due_at')->first();

        $this->patch(route('admin.case_assistances.cancel_installment', [$assistance, $installment]), ['notes' => 'Family travelled'])
            ->assertRedirect(route('admin.case_assistances.show', $assistance))
            ->assertSessionHas('success');

        $installment->refresh();
        $this->assertNotNull($installment->cancelled_at);
        $this->assertNull($installment->paid_at);
        $this->assertSame('Family travelled', $installment->notes);
        $this->assertSame('1000.00', $installment->amount);
    }

    #[Test]
    public function a_paid_or_already_cancelled_installment_cannot_be_cancelled(): void
    {
        $this->signInAdmin(['case_assistances.store', 'case_assistances.cancel_installment']);
        $assistance = $this->createAssistance($this->recurring());
        [$paid, $cancelled] = $assistance->installments()->orderBy('due_at')->take(2)->get()->all();
        $paid->forceFill(['paid_at' => now()])->save();
        $cancelled->forceFill(['cancelled_at' => now()])->save();

        $this->patch(route('admin.case_assistances.cancel_installment', [$assistance, $paid]))->assertSessionHasErrors('installment');
        $this->patch(route('admin.case_assistances.cancel_installment', [$assistance, $cancelled]))->assertSessionHasErrors('installment');

        $this->assertNull($paid->fresh()->cancelled_at);
        $this->assertNotNull($paid->fresh()->paid_at);
    }

    #[Test]
    public function an_installment_of_another_assistance_is_not_found(): void
    {
        $this->signInAdmin(['case_assistances.store', 'case_assistances.cancel_installment']);
        $first = $this->createAssistance($this->oneTime());
        $second = $this->createAssistance($this->oneTime());

        $this->patch(route('admin.case_assistances.cancel_installment', [$first, $second->installments()->first()]))->assertNotFound();
    }

    #[Test]
    public function nothing_in_this_module_records_a_payment(): void
    {
        $routes = collect(RouteFacade::getRoutes()->getRoutes())
            ->map(fn (Route $route) => (string) $route->getName())
            ->filter(fn (string $name) => str_contains($name, 'case_assistances') || str_contains($name, 'charity_cases'));

        $this->assertNotEmpty($routes);
        $this->assertEmpty($routes->filter(fn (string $name) => str_contains($name, 'pay')));
        $this->assertNotContains('paid_at', (new AssistanceInstallment)->getFillable());
        $this->assertNull(AssistanceInstallment::make(['paid_at' => now()])->paid_at);
    }

    #[Test]
    public function an_admin_without_the_permission_cannot_create_an_assistance(): void
    {
        $this->signInAdmin(['case_assistances.index']);

        $this->post(route('admin.case_assistances.store'), $this->oneTime())->assertRedirect(route('admin.unauthorized'));

        $this->assertSame(0, CaseAssistance::count());
    }
}

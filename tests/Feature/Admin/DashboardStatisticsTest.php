<?php

namespace Tests\Feature\Admin;

use App\Models\AssistanceInstallment;
use App\Models\CaseAssistance;
use App\Models\CharityCase;
use App\Models\User;
use Database\Seeders\LaratrustSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/** The charity case and assistance totals on the dashboard, shown to holders of `statistics`. */
class DashboardStatisticsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LaratrustSeeder::class);
    }

    #[Test]
    public function admin_with_the_statistics_permission_sees_case_and_assistance_totals(): void
    {
        CharityCase::factory()->create();
        $oneTime = CaseAssistance::factory()->create();
        CaseAssistance::factory()->recurring()->create(['active' => false]);

        $installment = fn (int $amount, int $dueInDays) => AssistanceInstallment::factory()->for($oneTime, 'assistance')
            ->state(['amount' => $amount, 'due_at' => today()->addDays($dueInDays)]);

        $installment(700, -10)->paid()->create();
        $installment(300, -3)->create();
        $installment(200, 5)->create();
        $installment(900, 12)->create()->forceFill(['cancelled_at' => now()])->save();

        $admin = User::factory()->admin()->create();
        $admin->givePermission('statistics');

        $this->actingAs($admin, 'web')
            ->get(route('admin.index'))
            ->assertOk()
            ->assertViewHas('sections', function (array $sections): bool {
                $values = collect($sections)->flatMap(fn (array $section) => $section['cards'])->pluck('value', 'label');

                return $values[__('Total Cases')] === 3
                    && $values[__('Cases With Assistances')] === 2
                    && $values[__('Cases Without Assistances')] === 1
                    && $values[__('Total Assistances')] === 2
                    && $values[__('Active Assistances')] === 1
                    && $values[__('One-time')] === 1
                    && $values[__('Monthly')] === 1
                    && $values[__('Paid Total')] === 700.0
                    && $values[__('Remaining Amount')] === 500.0
                    && $values[__('Overdue Installments')] === 1;
            });
    }

    #[Test]
    public function admin_without_the_statistics_permission_sees_only_the_general_cards(): void
    {
        $this->actingAs(User::factory()->admin()->create(), 'web')
            ->get(route('admin.index'))
            ->assertOk()
            ->assertViewHas('sections', fn (array $sections): bool => count($sections) === 1)
            ->assertDontSee(__('Total Assistances'));
    }
}

<?php

namespace Tests\Feature;

use App\Enums\UserTypeEnum;
use App\Models\AssistanceInstallment;
use App\Models\CaseAssistance;
use App\Models\CharityCase;
use Database\Seeders\CaseAssistanceSeeder;
use Database\Seeders\CaseCategorySeeder;
use Database\Seeders\CharityCaseSeeder;
use Database\Seeders\LaratrustSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/** CharityCaseSeeder seeds the beneficiaries and CaseAssistanceSeeder their plans with real schedules. */
class CharityCaseSeederTest extends TestCase
{
    use RefreshDatabase;

    private function seedCases(): void
    {
        $this->seed([
            LaratrustSeeder::class,
            UserSeeder::class,
            CaseCategorySeeder::class,
            CharityCaseSeeder::class,
            CaseAssistanceSeeder::class,
        ]);
    }

    #[Test]
    public function every_case_is_seeded_with_a_valid_profile_and_an_assistance(): void
    {
        $this->seedCases();

        $this->assertSame(count(CharityCaseSeeder::CASES), CharityCase::count());

        CharityCase::with('volunteer')->get()->each(function (CharityCase $charityCase): void {
            $this->assertMatchesRegularExpression('/^[23][0-9]{13}$/', $charityCase->national_id);
            $this->assertMatchesRegularExpression('/^01[0125][0-9]{8}$/', $charityCase->phone);
            $this->assertSame(UserTypeEnum::Volunteer->value, $charityCase->volunteer->type);
            $this->assertTrue($charityCase->assistances()->exists(), "{$charityCase->full_name} has no assistance.");
        });
    }

    #[Test]
    public function every_assistance_has_installments_with_paid_and_overdue_ones(): void
    {
        $this->seedCases();

        CaseAssistance::with('category')->get()->each(function (CaseAssistance $assistance): void {
            $this->assertNotNull($assistance->category);
            $this->assertTrue($assistance->installments()->exists());
        });

        $this->assertTrue(AssistanceInstallment::whereNotNull('paid_at')->exists());
        $this->assertTrue(AssistanceInstallment::whereNull('paid_at')->whereDate('due_at', '<', today())->exists());
    }

    #[Test]
    public function running_the_seeders_again_adds_nothing(): void
    {
        $this->seedCases();
        $counts = [CharityCase::count(), CaseAssistance::count(), AssistanceInstallment::count()];

        $this->seed([CharityCaseSeeder::class, CaseAssistanceSeeder::class]);

        $this->assertSame($counts, [CharityCase::count(), CaseAssistance::count(), AssistanceInstallment::count()]);
    }
}

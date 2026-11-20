<?php

namespace Database\Seeders;

use App\Enums\AssistanceTypeEnum;
use App\Enums\UserTypeEnum;
use App\Models\AssistanceInstallment;
use App\Models\CaseCategory;
use App\Models\CharityCase;
use App\Models\User;
use App\Services\CharityCase\CaseAssistanceService;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

/**
 * Support plans for the seeded beneficiaries, created through
 * CaseAssistanceService so every plan gets its real installment schedule.
 *
 * Each case gets the plan of its main need (CharityCaseSeeder::CASES), filed
 * under that category; every third case also gets a one-time aid, filed under
 * "Other". Monthly plans start a few months back, so the schedule holds
 * paid, overdue and upcoming installments: installments already due are
 * marked paid (standing in for the payments module), except on every fourth
 * case, which is left overdue.
 *
 * Seeded plans start in the past on purpose, to show a real history; an admin
 * cannot do that, since CaseAssistanceRequest refuses past dates.
 *
 * Idempotent: a case that already has an assistance is skipped. Run after
 * CaseCategorySeeder and CharityCaseSeeder.
 */
class CaseAssistanceSeeder extends Seeder
{
    /**
     * The main plan of each category (keyed by the category's English name).
     *
     * @var array<string, array{type: AssistanceTypeEnum, amount: int, title: array{en: string, ar: string}, description: array{en: string, ar: string}}>
     */
    public const PLANS = [
        'Food' => [
            'type' => AssistanceTypeEnum::Recurring,
            'amount' => 1200,
            'title' => ['en' => 'Monthly food basket', 'ar' => 'كرتونة غذائية شهرية'],
            'description' => ['en' => 'Basic groceries for the family every month.', 'ar' => 'سلع تموينية أساسية للأسرة كل شهر.'],
        ],
        'Housing' => [
            'type' => AssistanceTypeEnum::Recurring,
            'amount' => 2000,
            'title' => ['en' => 'Rent support', 'ar' => 'دعم إيجار السكن'],
            'description' => ['en' => 'A contribution to the monthly rent.', 'ar' => 'المساهمة في إيجار المسكن الشهري.'],
        ],
        'Medical' => [
            'type' => AssistanceTypeEnum::Recurring,
            'amount' => 1500,
            'title' => ['en' => 'Monthly medication', 'ar' => 'علاج شهري'],
            'description' => ['en' => 'Monthly medicine for a chronic illness.', 'ar' => 'تكلفة الأدوية الشهرية لمرض مزمن.'],
        ],
        'Education' => [
            'type' => AssistanceTypeEnum::OneTime,
            'amount' => 3500,
            'title' => ['en' => 'School fees', 'ar' => 'مصروفات دراسية'],
            'description' => ['en' => 'School fees and school supplies.', 'ar' => 'سداد المصروفات الدراسية وشراء الأدوات المدرسية.'],
        ],
        'Other' => [
            'type' => AssistanceTypeEnum::OneTime,
            'amount' => 5000,
            'title' => ['en' => 'Emergency aid', 'ar' => 'مساعدة طارئة'],
            'description' => ['en' => 'Urgent help for an emergency.', 'ar' => 'مساعدة عاجلة لظرف طارئ.'],
        ],
    ];

    /**
     * The extra one-time aid every third case receives.
     *
     * @var array{type: AssistanceTypeEnum, amount: int, title: array{en: string, ar: string}, description: array{en: string, ar: string}}
     */
    public const EXTRA_PLAN = [
        'type' => AssistanceTypeEnum::OneTime,
        'amount' => 2500,
        'title' => ['en' => 'Eid clothing', 'ar' => 'كسوة العيد'],
        'description' => ['en' => 'Eid clothes for the family.', 'ar' => 'ملابس العيد لأفراد الأسرة.'],
    ];

    /** How many months before this month the monthly plans start. */
    private const MONTHS_BACK = 3;

    public function __construct(private readonly CaseAssistanceService $assistanceService) {}

    public function run(): void
    {
        $creator = User::query()
            ->whereIn('type', [UserTypeEnum::Admin->value, UserTypeEnum::SuperAdmin->value])
            ->orderBy('id')
            ->first();

        if (! $creator) {
            $this->command?->warn('CaseAssistanceSeeder skipped: there is no admin to record as the creator.');

            return;
        }

        $categories = CaseCategory::query()
            ->get()
            ->mapWithKeys(fn (CaseCategory $category): array => [$category->nameLang('en') => $category->id]);

        if ($categories->isEmpty()) {
            $this->command?->warn('CaseAssistanceSeeder skipped: run CaseCategorySeeder first.');

            return;
        }

        $needs = collect(CharityCaseSeeder::CASES)->pluck('category', 'national_id');

        $cases = CharityCase::query()
            ->whereDoesntHave('assistances')
            ->orderBy('id')
            ->get();

        foreach ($cases->values() as $index => $charityCase) {
            $need = $needs->get($charityCase->national_id, 'Other');
            $plans = [[$need, self::PLANS[$need] ?? self::PLANS['Other']]];

            if ($index % 3 === 0) {
                $plans[] = ['Other', self::EXTRA_PLAN];
            }

            foreach ($plans as $order => [$categoryName, $plan]) {
                $categoryId = $categories->get($categoryName) ?? $categories->first();

                $this->assistanceService->create($this->assistanceData($charityCase, $categoryId, $plan, $index, $order + 1), $creator);
            }

            if ($index % 4 !== 3) {
                $this->payDueInstallments($charityCase, $creator);
            }
        }
    }

    /**
     * The same data a validated CaseAssistanceRequest would hold.
     *
     * @param  array{type: AssistanceTypeEnum, amount: int, title: array{en: string, ar: string}, description: array{en: string, ar: string}}  $plan
     * @return array<string, mixed>
     */
    private function assistanceData(CharityCase $charityCase, int $categoryId, array $plan, int $index, int $order): array
    {
        $today = CarbonImmutable::today();
        $data = [
            'case_id' => $charityCase->id,
            'category_id' => $categoryId,
            'assistance_type' => $plan['type']->value,
            'title' => $plan['title'],
            'description' => $plan['description'],
            'order_id' => $order,
            'active' => 1,
        ];

        if ($plan['type'] === AssistanceTypeEnum::Recurring) {
            return $data + [
                'amount_per_period' => $plan['amount'],
                'start_month' => $today->startOfMonth()->subMonths(self::MONTHS_BACK)->format('Y-m'),
                'end_month' => null,
            ];
        }

        // Alternate one-time aids between last week (already due) and the coming weeks.
        $dueAt = $index % 2 === 0 ? $today->subDays(7) : $today->addDays(10 + $index);

        return $data + [
            'requested_amount' => $plan['amount'],
            'due_at' => $dueAt->toDateString(),
        ];
    }

    /** Marks the case's installments due by today as paid, as the payments module would. */
    private function payDueInstallments(CharityCase $charityCase, User $payer): void
    {
        $charityCase->installments()
            ->whereDate('due_at', '<=', today())
            ->whereNull('assistance_installments.paid_at')
            ->whereNull('assistance_installments.cancelled_at')
            ->get()
            ->each(fn (AssistanceInstallment $installment) => $installment->forceFill([
                'paid_at' => CarbonImmutable::parse($installment->due_at)->setTime(10, 0),
                'paid_by' => $payer->id,
            ])->save());
    }
}

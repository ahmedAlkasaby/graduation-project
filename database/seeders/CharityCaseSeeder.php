<?php

namespace Database\Seeders;

use App\Enums\UserTypeEnum;
use App\Models\CharityCase;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Realistic beneficiary profiles, handed round-robin to the active volunteers.
 * `category` is each beneficiary's main need: CaseAssistanceSeeder files their
 * assistance under it.
 *
 * Idempotent: a case is matched on its national ID. Run after UserSeeder (it
 * needs at least one active volunteer).
 */
class CharityCaseSeeder extends Seeder
{
    /**
     * Each national ID follows the Egyptian layout: century (2 = 1900s, 3 = 2000s),
     * birth date YYMMDD, governorate code, serial and a check digit.
     *
     * @var list<array{full_name: string, national_id: string, phone: string, category: string}>
     */
    public const CASES = [
        ['full_name' => 'فاطمة محمد عبد الحميد', 'national_id' => '27805120101234', 'phone' => '01012345671', 'category' => 'Food'],
        ['full_name' => 'أحمد سيد عبد العزيز', 'national_id' => '26511030201457', 'phone' => '01123456782', 'category' => 'Medical'],
        ['full_name' => 'زينب علي حسن', 'national_id' => '28207150101863', 'phone' => '01234567893', 'category' => 'Housing'],
        ['full_name' => 'محمود إبراهيم السيد', 'national_id' => '29001200301295', 'phone' => '01512345674', 'category' => 'Education'],
        ['full_name' => 'سعاد أحمد مصطفى', 'national_id' => '25909080101346', 'phone' => '01098765435', 'category' => 'Food'],
        ['full_name' => 'خالد عبد الله رمضان', 'national_id' => '28403211201578', 'phone' => '01187654326', 'category' => 'Medical'],
        ['full_name' => 'نادية حسين محمود', 'national_id' => '27112040101682', 'phone' => '01276543217', 'category' => 'Food'],
        ['full_name' => 'عبد الرحمن جمال فتحي', 'national_id' => '30105180201914', 'phone' => '01565432108', 'category' => 'Education'],
        ['full_name' => 'هدى عبد الفتاح سالم', 'national_id' => '26806250301125', 'phone' => '01054321099', 'category' => 'Housing'],
        ['full_name' => 'سامي فؤاد عبد المنعم', 'national_id' => '25602140101736', 'phone' => '01143210981', 'category' => 'Medical'],
        ['full_name' => 'أم هاشم سيد أحمد', 'national_id' => '26210300101547', 'phone' => '01232109872', 'category' => 'Food'],
        ['full_name' => 'مصطفى كمال يوسف', 'national_id' => '29308090201368', 'phone' => '01521098763', 'category' => 'Other'],
        ['full_name' => 'رحاب عادل منصور', 'national_id' => '28812010101259', 'phone' => '01010293844', 'category' => 'Education'],
        ['full_name' => 'جمال عبد الناصر شحاتة', 'national_id' => '26004170301471', 'phone' => '01129384755', 'category' => 'Housing'],
        ['full_name' => 'وفاء محمد الشربيني', 'national_id' => '27509220101692', 'phone' => '01238475666', 'category' => 'Medical'],
        ['full_name' => 'حسن علي عبد الرازق', 'national_id' => '26901110201813', 'phone' => '01547566577', 'category' => 'Food'],
        ['full_name' => 'منى سعيد عبد الغني', 'national_id' => '28606060101924', 'phone' => '01056677488', 'category' => 'Other'],
        ['full_name' => 'يوسف طارق البنا', 'national_id' => '30307300101135', 'phone' => '01165788399', 'category' => 'Education'],
        ['full_name' => 'عزة محمود الدسوقي', 'national_id' => '27310190301346', 'phone' => '01274899210', 'category' => 'Food'],
        ['full_name' => 'إبراهيم عوض الله', 'national_id' => '25811050201557', 'phone' => '01583900121', 'category' => 'Medical'],
    ];

    public function run(): void
    {
        $volunteers = User::query()
            ->ofType(UserTypeEnum::Volunteer->value)
            ->where('active', true)
            ->orderBy('id')
            ->pluck('id')
            ->all();

        if ($volunteers === []) {
            $this->command?->warn('CharityCaseSeeder skipped: there is no active volunteer to follow the cases.');

            return;
        }

        foreach (self::CASES as $index => $case) {
            // The national ID is unique across soft deleted rows too, so a trashed seed case is restored, not duplicated.
            $charityCase = CharityCase::withTrashed()->updateOrCreate(
                ['national_id' => $case['national_id']],
                [
                    'full_name' => $case['full_name'],
                    'phone' => $case['phone'],
                    'volunteer_id' => $volunteers[$index % count($volunteers)],
                ],
            );

            if ($charityCase->trashed()) {
                $charityCase->restore();
            }
        }
    }
}

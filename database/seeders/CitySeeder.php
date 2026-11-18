<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use Illuminate\Database\Seeder;

/**
 * Egypt's 27 governorates (محافظات), stored as cities of Egypt.
 *
 * Idempotent: a governorate is matched on its English name, so running it
 * again refreshes the rows instead of duplicating them. Run after
 * CountrySeeder (Egypt must exist).
 */
class CitySeeder extends Seeder
{
    /**
     * @var list<array{ar: string, en: string, latitude: string, longitude: string}>
     */
    public const GOVERNORATES = [
        ['ar' => 'القاهرة', 'en' => 'Cairo', 'latitude' => '30.0444', 'longitude' => '31.2357'],
        ['ar' => 'الجيزة', 'en' => 'Giza', 'latitude' => '30.0131', 'longitude' => '31.2089'],
        ['ar' => 'الإسكندرية', 'en' => 'Alexandria', 'latitude' => '31.2001', 'longitude' => '29.9187'],
        ['ar' => 'القليوبية', 'en' => 'Qalyubia', 'latitude' => '30.4659', 'longitude' => '31.1848'],
        ['ar' => 'الشرقية', 'en' => 'Sharqia', 'latitude' => '30.5877', 'longitude' => '31.5020'],
        ['ar' => 'الدقهلية', 'en' => 'Dakahlia', 'latitude' => '31.0409', 'longitude' => '31.3785'],
        ['ar' => 'الغربية', 'en' => 'Gharbia', 'latitude' => '30.7865', 'longitude' => '31.0004'],
        ['ar' => 'المنوفية', 'en' => 'Monufia', 'latitude' => '30.5972', 'longitude' => '30.9876'],
        ['ar' => 'البحيرة', 'en' => 'Beheira', 'latitude' => '31.0341', 'longitude' => '30.4682'],
        ['ar' => 'كفر الشيخ', 'en' => 'Kafr El Sheikh', 'latitude' => '31.1107', 'longitude' => '30.9388'],
        ['ar' => 'دمياط', 'en' => 'Damietta', 'latitude' => '31.4165', 'longitude' => '31.8133'],
        ['ar' => 'بورسعيد', 'en' => 'Port Said', 'latitude' => '31.2653', 'longitude' => '32.3019'],
        ['ar' => 'الإسماعيلية', 'en' => 'Ismailia', 'latitude' => '30.5965', 'longitude' => '32.2715'],
        ['ar' => 'السويس', 'en' => 'Suez', 'latitude' => '29.9668', 'longitude' => '32.5498'],
        ['ar' => 'شمال سيناء', 'en' => 'North Sinai', 'latitude' => '31.1316', 'longitude' => '33.7984'],
        ['ar' => 'جنوب سيناء', 'en' => 'South Sinai', 'latitude' => '28.2350', 'longitude' => '33.6176'],
        ['ar' => 'الفيوم', 'en' => 'Faiyum', 'latitude' => '29.3084', 'longitude' => '30.8428'],
        ['ar' => 'بني سويف', 'en' => 'Beni Suef', 'latitude' => '29.0661', 'longitude' => '31.0994'],
        ['ar' => 'المنيا', 'en' => 'Minya', 'latitude' => '28.1099', 'longitude' => '30.7503'],
        ['ar' => 'أسيوط', 'en' => 'Asyut', 'latitude' => '27.1783', 'longitude' => '31.1859'],
        ['ar' => 'سوهاج', 'en' => 'Sohag', 'latitude' => '26.5591', 'longitude' => '31.6957'],
        ['ar' => 'قنا', 'en' => 'Qena', 'latitude' => '26.1551', 'longitude' => '32.7160'],
        ['ar' => 'الأقصر', 'en' => 'Luxor', 'latitude' => '25.6872', 'longitude' => '32.6396'],
        ['ar' => 'أسوان', 'en' => 'Aswan', 'latitude' => '24.0889', 'longitude' => '32.8998'],
        ['ar' => 'البحر الأحمر', 'en' => 'Red Sea', 'latitude' => '27.2579', 'longitude' => '33.8116'],
        ['ar' => 'الوادي الجديد', 'en' => 'New Valley', 'latitude' => '25.4390', 'longitude' => '30.5586'],
        ['ar' => 'مطروح', 'en' => 'Matrouh', 'latitude' => '31.3543', 'longitude' => '27.2373'],
    ];

    public function run(): void
    {
        $egyptId = Country::where('code', 'EG')->value('id');

        foreach (self::GOVERNORATES as $index => $governorate) {
            $city = City::where('country_id', $egyptId)
                ->where('name->en', $governorate['en'])
                ->first() ?? new City(['country_id' => $egyptId]);

            $city->fill([
                'name' => ['ar' => $governorate['ar'], 'en' => $governorate['en']],
                'latitude' => $governorate['latitude'],
                'longitude' => $governorate['longitude'],
                'order_id' => $index + 1,
                'active' => 1,
            ])->save();
        }
    }
}

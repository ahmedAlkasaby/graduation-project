<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Region;
use Illuminate\Database\Seeder;

/**
 * The best-known areas and districts (مناطق / مراكز) of each Egyptian
 * governorate, keyed by the governorate's English name from CitySeeder.
 *
 * Idempotent: a region is matched on its city and English name. Run after
 * CitySeeder.
 */
class RegionSeeder extends Seeder
{
    /**
     * @var array<string, list<array{0: string, 1: string}>> governorate (en) => [[ar, en], ...]
     */
    public const REGIONS = [
        'Cairo' => [
            ['مدينة نصر', 'Nasr City'],
            ['مصر الجديدة', 'Heliopolis'],
            ['المعادي', 'Maadi'],
            ['التجمع الخامس', 'Fifth Settlement'],
            ['القاهرة الجديدة', 'New Cairo'],
            ['الزمالك', 'Zamalek'],
            ['وسط البلد', 'Downtown'],
            ['شبرا', 'Shubra'],
            ['حلوان', 'Helwan'],
            ['المقطم', 'Mokattam'],
            ['عين شمس', 'Ain Shams'],
            ['المرج', 'El Marg'],
            ['الشروق', 'El Shorouk'],
            ['مدينة بدر', 'Badr City'],
            ['السيدة زينب', 'Sayeda Zeinab'],
            ['المطرية', 'El Matareya'],
        ],
        'Giza' => [
            ['الدقي', 'Dokki'],
            ['المهندسين', 'Mohandessin'],
            ['العجوزة', 'Agouza'],
            ['الهرم', 'Haram'],
            ['فيصل', 'Faisal'],
            ['إمبابة', 'Imbaba'],
            ['بولاق الدكرور', 'Boulaq El Dakrour'],
            ['الشيخ زايد', 'Sheikh Zayed'],
            ['6 أكتوبر', '6th of October'],
            ['حدائق الأهرام', 'Hadayek El Ahram'],
            ['الحوامدية', 'El Hawamdeya'],
            ['البدرشين', 'El Badrashein'],
            ['العياط', 'El Ayat'],
            ['أوسيم', 'Ausim'],
        ],
        'Alexandria' => [
            ['سموحة', 'Smouha'],
            ['سيدي جابر', 'Sidi Gaber'],
            ['ستانلي', 'Stanley'],
            ['المنتزه', 'Montaza'],
            ['ميامي', 'Miami'],
            ['سيدي بشر', 'Sidi Bishr'],
            ['العجمي', 'Agami'],
            ['المندرة', 'Mandara'],
            ['محطة الرمل', 'Raml Station'],
            ['العصافرة', 'Asafra'],
            ['برج العرب', 'Borg El Arab'],
            ['المنشية', 'Mansheya'],
            ['كليوباترا', 'Cleopatra'],
            ['الدخيلة', 'El Dekheila'],
        ],
        'Qalyubia' => [
            ['بنها', 'Banha'],
            ['شبرا الخيمة', 'Shubra El Kheima'],
            ['قليوب', 'Qalyub'],
            ['القناطر الخيرية', 'El Qanater El Khayreya'],
            ['الخانكة', 'El Khanka'],
            ['العبور', 'El Obour'],
            ['طوخ', 'Toukh'],
            ['كفر شكر', 'Kafr Shukr'],
            ['شبين القناطر', 'Shibin El Qanater'],
            ['الخصوص', 'El Khusus'],
        ],
        'Sharqia' => [
            ['الزقازيق', 'Zagazig'],
            ['العاشر من رمضان', '10th of Ramadan'],
            ['بلبيس', 'Belbeis'],
            ['منيا القمح', 'Minya El Qamh'],
            ['أبو حماد', 'Abu Hammad'],
            ['فاقوس', 'Faqous'],
            ['ههيا', 'Hihya'],
            ['أبو كبير', 'Abu Kabir'],
            ['ديرب نجم', 'Diyarb Negm'],
            ['الحسينية', 'El Husseiniya'],
        ],
        'Dakahlia' => [
            ['المنصورة', 'Mansoura'],
            ['طلخا', 'Talkha'],
            ['ميت غمر', 'Mit Ghamr'],
            ['السنبلاوين', 'El Senbellawein'],
            ['دكرنس', 'Dikirnis'],
            ['أجا', 'Aga'],
            ['بلقاس', 'Belqas'],
            ['شربين', 'Sherbin'],
            ['المنزلة', 'El Manzala'],
            ['جمصة', 'Gamasa'],
        ],
        'Gharbia' => [
            ['طنطا', 'Tanta'],
            ['المحلة الكبرى', 'El Mahalla El Kubra'],
            ['كفر الزيات', 'Kafr El Zayat'],
            ['زفتى', 'Zefta'],
            ['السنطة', 'El Santa'],
            ['سمنود', 'Samanoud'],
            ['بسيون', 'Basyoun'],
            ['قطور', 'Qutur'],
        ],
        'Monufia' => [
            ['شبين الكوم', 'Shibin El Kom'],
            ['مدينة السادات', 'Sadat City'],
            ['منوف', 'Menouf'],
            ['أشمون', 'Ashmoun'],
            ['الباجور', 'El Bagour'],
            ['قويسنا', 'Quesna'],
            ['بركة السبع', 'Berket El Saba'],
            ['تلا', 'Tala'],
            ['الشهداء', 'El Shohada'],
        ],
        'Beheira' => [
            ['دمنهور', 'Damanhour'],
            ['كفر الدوار', 'Kafr El Dawar'],
            ['رشيد', 'Rashid'],
            ['إدكو', 'Edku'],
            ['أبو حمص', 'Abu Hummus'],
            ['إيتاي البارود', 'Itay El Barud'],
            ['كوم حمادة', 'Kom Hamada'],
            ['وادي النطرون', 'Wadi El Natrun'],
            ['حوش عيسى', 'Hosh Issa'],
            ['أبو المطامير', 'Abu El Matamir'],
        ],
        'Kafr El Sheikh' => [
            ['مدينة كفر الشيخ', 'Kafr El Sheikh City'],
            ['دسوق', 'Desouk'],
            ['فوه', 'Fuwah'],
            ['بلطيم', 'Baltim'],
            ['سيدي سالم', 'Sidi Salem'],
            ['الحامول', 'El Hamool'],
            ['بيلا', 'Bila'],
            ['مطوبس', 'Metoubes'],
            ['قلين', 'Qallin'],
        ],
        'Damietta' => [
            ['مدينة دمياط', 'Damietta City'],
            ['دمياط الجديدة', 'New Damietta'],
            ['رأس البر', 'Ras El Bar'],
            ['فارسكور', 'Faraskour'],
            ['كفر سعد', 'Kafr Saad'],
            ['الزرقا', 'El Zarqa'],
            ['كفر البطيخ', 'Kafr El Battikh'],
        ],
        'Port Said' => [
            ['حي الشرق', 'El Sharq District'],
            ['حي العرب', 'El Arab District'],
            ['حي المناخ', 'El Manakh District'],
            ['حي الضواحي', 'El Dawahy District'],
            ['حي الزهور', 'El Zohour District'],
            ['بورفؤاد', 'Port Fouad'],
            ['حي الجنوب', 'El Ganoub District'],
        ],
        'Ismailia' => [
            ['مدينة الإسماعيلية', 'Ismailia City'],
            ['فايد', 'Fayed'],
            ['القنطرة شرق', 'El Qantara Sharq'],
            ['القنطرة غرب', 'El Qantara Gharb'],
            ['التل الكبير', 'El Tal El Kabir'],
            ['أبو صوير', 'Abu Suwir'],
            ['القصاصين', 'El Qassasin'],
        ],
        'Suez' => [
            ['حي السويس', 'Suez District'],
            ['حي الأربعين', 'El Arbaeen District'],
            ['حي عتاقة', 'Ataqah District'],
            ['حي الجناين', 'El Ganayen District'],
            ['حي فيصل', 'Faisal District'],
            ['العين السخنة', 'Ain Sokhna'],
        ],
        'North Sinai' => [
            ['العريش', 'Arish'],
            ['الشيخ زويد', 'Sheikh Zuweid'],
            ['رفح', 'Rafah'],
            ['بئر العبد', 'Bir al-Abd'],
            ['الحسنة', 'El Hasana'],
            ['نخل', 'Nakhl'],
        ],
        'South Sinai' => [
            ['شرم الشيخ', 'Sharm El Sheikh'],
            ['دهب', 'Dahab'],
            ['نويبع', 'Nuweiba'],
            ['طابا', 'Taba'],
            ['سانت كاترين', 'Saint Catherine'],
            ['الطور', 'El Tor'],
            ['رأس سدر', 'Ras Sedr'],
            ['أبو رديس', 'Abu Rudeis'],
        ],
        'Faiyum' => [
            ['مدينة الفيوم', 'Faiyum City'],
            ['الفيوم الجديدة', 'New Faiyum'],
            ['سنورس', 'Sinnuris'],
            ['إطسا', 'Itsa'],
            ['طامية', 'Tamiya'],
            ['أبشواي', 'Ibshaway'],
            ['يوسف الصديق', 'Youssef El Seddik'],
        ],
        'Beni Suef' => [
            ['مدينة بني سويف', 'Beni Suef City'],
            ['بني سويف الجديدة', 'New Beni Suef'],
            ['الواسطى', 'El Wasta'],
            ['ناصر', 'Nasser'],
            ['إهناسيا', 'Ihnasya'],
            ['ببا', 'Beba'],
            ['الفشن', 'El Fashn'],
            ['سمسطا', 'Somosta'],
        ],
        'Minya' => [
            ['مدينة المنيا', 'Minya City'],
            ['المنيا الجديدة', 'New Minya'],
            ['ملوي', 'Mallawi'],
            ['سمالوط', 'Samalut'],
            ['مطاي', 'Matai'],
            ['بني مزار', 'Beni Mazar'],
            ['مغاغة', 'Maghagha'],
            ['أبو قرقاص', 'Abu Qurqas'],
            ['دير مواس', 'Deir Mawas'],
            ['العدوة', 'El Edwa'],
        ],
        'Asyut' => [
            ['مدينة أسيوط', 'Asyut City'],
            ['أسيوط الجديدة', 'New Asyut'],
            ['ديروط', 'Dairut'],
            ['منفلوط', 'Manfalut'],
            ['القوصية', 'El Qusiya'],
            ['أبنوب', 'Abnub'],
            ['أبو تيج', 'Abu Tig'],
            ['صدفا', 'Sidfa'],
            ['البداري', 'El Badari'],
        ],
        'Sohag' => [
            ['مدينة سوهاج', 'Sohag City'],
            ['سوهاج الجديدة', 'New Sohag'],
            ['أخميم', 'Akhmim'],
            ['طهطا', 'Tahta'],
            ['جرجا', 'Girga'],
            ['البلينا', 'El Balyana'],
            ['المراغة', 'El Maragha'],
            ['طما', 'Tama'],
            ['المنشأة', 'El Monshah'],
        ],
        'Qena' => [
            ['مدينة قنا', 'Qena City'],
            ['نجع حمادي', 'Nag Hammadi'],
            ['قوص', 'Qus'],
            ['دشنا', 'Deshna'],
            ['أبو تشت', 'Abu Tesht'],
            ['قفط', 'Qift'],
            ['فرشوط', 'Farshut'],
            ['نقادة', 'Naqada'],
        ],
        'Luxor' => [
            ['مدينة الأقصر', 'Luxor City'],
            ['البر الغربي', 'West Bank'],
            ['الكرنك', 'Karnak'],
            ['إسنا', 'Esna'],
            ['أرمنت', 'Armant'],
            ['الطود', 'El Tod'],
            ['القرنة', 'El Qurna'],
        ],
        'Aswan' => [
            ['مدينة أسوان', 'Aswan City'],
            ['أسوان الجديدة', 'New Aswan'],
            ['كوم أمبو', 'Kom Ombo'],
            ['إدفو', 'Edfu'],
            ['دراو', 'Daraw'],
            ['نصر النوبة', 'Nasr El Nuba'],
            ['أبو سمبل', 'Abu Simbel'],
        ],
        'Red Sea' => [
            ['الغردقة', 'Hurghada'],
            ['الجونة', 'El Gouna'],
            ['سفاجا', 'Safaga'],
            ['القصير', 'El Quseir'],
            ['مرسى علم', 'Marsa Alam'],
            ['رأس غارب', 'Ras Gharib'],
            ['الشلاتين', 'Shalateen'],
        ],
        'New Valley' => [
            ['الخارجة', 'Kharga'],
            ['الداخلة', 'Dakhla'],
            ['الفرافرة', 'Farafra'],
            ['باريس', 'Paris'],
            ['بلاط', 'Balat'],
        ],
        'Matrouh' => [
            ['مرسى مطروح', 'Marsa Matrouh'],
            ['العلمين', 'El Alamein'],
            ['العلمين الجديدة', 'New Alamein'],
            ['الساحل الشمالي', 'North Coast'],
            ['الضبعة', 'El Dabaa'],
            ['سيوة', 'Siwa'],
            ['السلوم', 'El Salloum'],
            ['الحمام', 'El Hammam'],
            ['سيدي براني', 'Sidi Barrani'],
        ],
    ];

    public function run(): void
    {
        $cities = City::all()->keyBy(fn (City $city) => $city->name['en'] ?? null);

        foreach (self::REGIONS as $governorate => $regions) {
            $city = $cities->get($governorate);

            if (! $city) {
                continue;
            }

            foreach ($regions as $index => [$arabicName, $englishName]) {
                $region = Region::where('city_id', $city->id)
                    ->where('name->en', $englishName)
                    ->first() ?? new Region(['city_id' => $city->id]);

                $region->fill([
                    'name' => ['ar' => $arabicName, 'en' => $englishName],
                    'order_id' => $index + 1,
                    'active' => 1,
                ])->save();
            }
        }
    }
}

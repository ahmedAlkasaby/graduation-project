<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;
use App\Models\Country;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $egyptId = Country::where('code', 'EG')->value('id');

        $cities = [
            [
                'name'       => ['ar' => 'القاهرة', 'en' => 'Cairo'],
                'country_id' => $egyptId,
                'order_id'   => 1,
                'active'     => 1,
            ],
            [
                'name'       => ['ar' => 'الإسكندرية', 'en' => 'Alexandria'],
                'country_id' => $egyptId,
                'order_id'   => 2,
                'active'     => 1,
            ],
        ];

        foreach ($cities as $city) {
            City::create($city);
        }
    }
}

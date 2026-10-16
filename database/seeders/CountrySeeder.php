<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            [
                'name' => ['ar' => 'مصر', 'en' => 'Egypt'],
                'code' => 'EG',
                'image' => '/uploads/flags/eg.png',
                'phone_code' => '2',
                'order_id' => 1,
                'active' => 1,
            ],
        ];

        foreach ($countries as $country) {
            Country::firstOrCreate(['code' => $country['code']], $country);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CountrySeeder::class,
            CitySeeder::class,
            RegionSeeder::class,
            LaratrustSeeder::class,
            AdminUserSeeder::class,
            SettingSeeder::class,
            CaseCategorySeeder::class,
        ]);

        if (! app()->isProduction()) {
            $this->call([
                UserSeeder::class,
                CharityCaseSeeder::class,
                CaseAssistanceSeeder::class,
            ]);
        }
    }
}

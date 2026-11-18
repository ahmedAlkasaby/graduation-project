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
            LaratrustSeeder::class,
            AdminUserSeeder::class,
            SettingSeeder::class,
        ]);

        if (! app()->isProduction()) {
            $this->call(UserSeeder::class);
        }
    }
}

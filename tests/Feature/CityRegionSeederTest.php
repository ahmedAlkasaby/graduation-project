<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Region;
use Database\Seeders\CitySeeder;
use Database\Seeders\CountrySeeder;
use Database\Seeders\RegionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/** CitySeeder seeds Egypt's governorates and RegionSeeder their well-known areas. */
class CityRegionSeederTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function every_governorate_is_seeded_with_its_regions(): void
    {
        $this->seed([CountrySeeder::class, CitySeeder::class, RegionSeeder::class]);

        $this->assertSame(27, City::count());
        $this->assertSame(
            collect(RegionSeeder::REGIONS)->flatten(1)->count(),
            Region::count(),
        );

        City::all()->each(fn (City $city) => $this->assertTrue(
            $city->regions()->exists(),
            "{$city->name['en']} has no regions.",
        ));

        $cairo = City::where('name->en', 'Cairo')->first();
        $this->assertSame('القاهرة', $cairo->name['ar']);
        $this->assertTrue($cairo->regions()->where('name->en', 'Nasr City')->exists());
    }

    #[Test]
    public function running_the_seeders_again_does_not_duplicate_rows(): void
    {
        $this->seed([CountrySeeder::class, CitySeeder::class, RegionSeeder::class]);
        $regionCount = Region::count();

        $this->seed([CitySeeder::class, RegionSeeder::class]);

        $this->assertSame(27, City::count());
        $this->assertSame($regionCount, Region::count());
    }
}

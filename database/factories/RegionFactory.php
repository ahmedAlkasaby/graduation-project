<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Region>
 */
class RegionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->streetName();

        return [
            'name' => ['en' => $name, 'ar' => $name],
            'city_id' => City::factory(),
            'order_id' => 1,
            'active' => 1,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['active' => 0]);
    }
}

<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\CharityCase;
use App\Models\City;
use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Builds an address for a beneficiary by default; use `for($model, 'model')`
 * to attach it to another model. The region, when asked for, belongs to the
 * address's own city.
 *
 * @extends Factory<Address>
 */
class AddressFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'model_type' => (new CharityCase)->getMorphClass(),
            'model_id' => CharityCase::factory(),
            'city_id' => City::factory(),
            'region_id' => null,
            'address' => null,
            'latitude' => null,
            'longitude' => null,
        ];
    }

    /** Fills every optional part: the region (of the same city), the text address and the map point. */
    public function complete(): static
    {
        return $this->state(fn (): array => [
            // Resolved after city_id, so it receives the city that was just made or given.
            'region_id' => fn (array $attributes): int => Region::factory()->create(['city_id' => $attributes['city_id']])->id,
            'address' => fake()->streetAddress(),
            'latitude' => fake()->latitude(22, 31),
            'longitude' => fake()->longitude(25, 35),
        ]);
    }
}

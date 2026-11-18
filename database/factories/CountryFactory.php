<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Country>
 */
class CountryFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->country();

        return [
            'name' => ['en' => $name, 'ar' => $name],
            'code' => fake()->unique()->countryCode(),
            'phone_code' => (string) fake()->numberBetween(1, 999),
            'order_id' => 1,
            'active' => 1,
        ];
    }
}

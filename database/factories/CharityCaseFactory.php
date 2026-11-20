<?php

namespace Database\Factories;

use App\Models\CharityCase;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CharityCase>
 */
class CharityCaseFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),
            'national_id' => fake()->unique()->numerify('2#############'),
            'phone' => '010'.fake()->numerify('########'),
            'volunteer_id' => User::factory()->volunteer(),
        ];
    }
}

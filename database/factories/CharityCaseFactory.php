<?php

namespace Database\Factories;

use App\Models\CaseCategory;
use App\Models\CharityCase;
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
            'category_id' => CaseCategory::factory(),
            'volunteer_id' => null,
        ];
    }

    public function withoutNationalId(): static
    {
        return $this->state(fn () => ['national_id' => null]);
    }
}

<?php

namespace Database\Factories;

use App\Models\CaseCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CaseCategory>
 */
class CaseCategoryFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->word();

        return [
            'name' => ['en' => $name, 'ar' => $name],
            'order_id' => 1,
            'active' => 1,
        ];
    }
}

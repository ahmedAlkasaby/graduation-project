<?php

namespace Database\Factories;

use App\Enums\UserTypeEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => '010'.fake()->unique()->numerify('########'),
            'password' => 'password',
            'type' => UserTypeEnum::Donor->value,
            'active' => true,
            'locale' => 'en',
            'email_verified_at' => now(),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => ['type' => UserTypeEnum::Admin->value]);
    }

    public function superAdmin(): static
    {
        return $this->state(fn () => ['type' => UserTypeEnum::SuperAdmin->value]);
    }

    public function volunteer(): static
    {
        return $this->state(fn () => ['type' => UserTypeEnum::Volunteer->value]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['active' => false]);
    }
}

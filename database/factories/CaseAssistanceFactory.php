<?php

namespace Database\Factories;

use App\Enums\AssistanceTypeEnum;
use App\Models\CaseAssistance;
use App\Models\CharityCase;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * A one-time assistance by default. Installments are not generated here:
 * create assistances with their schedule through CaseAssistanceService.
 *
 * @extends Factory<CaseAssistance>
 */
class CaseAssistanceFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(3);

        return [
            'case_id' => CharityCase::factory(),
            'assistance_type' => AssistanceTypeEnum::OneTime,
            'title' => ['en' => $title, 'ar' => $title],
            'description' => null,
            'requested_amount' => 5000,
            'amount_per_period' => null,
            'order_id' => 1,
            'active' => 1,
            'created_by' => User::factory()->admin(),
        ];
    }

    public function recurring(int|float $amountPerPeriod = 1000): static
    {
        return $this->state(fn () => [
            'assistance_type' => AssistanceTypeEnum::Recurring,
            'requested_amount' => null,
            'amount_per_period' => $amountPerPeriod,
        ]);
    }
}

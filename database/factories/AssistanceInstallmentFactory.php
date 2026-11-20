<?php

namespace Database\Factories;

use App\Models\AssistanceInstallment;
use App\Models\CaseAssistance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssistanceInstallment>
 */
class AssistanceInstallmentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'case_assistance_id' => CaseAssistance::factory(),
            'due_at' => now()->addMonth()->startOfMonth()->toDateString(),
            'amount' => 1000,
            'notes' => null,
        ];
    }

    /** Stands in for the payments module, which will be the only writer of `paid_at`. */
    public function paid(): static
    {
        return $this->afterCreating(function (AssistanceInstallment $installment): void {
            $installment->forceFill(['paid_at' => now()])->save();
        });
    }
}

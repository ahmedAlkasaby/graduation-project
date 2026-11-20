<?php

namespace App\Services\CharityCase;

use App\Enums\AssistanceTypeEnum;
use App\Models\AssistanceInstallment;
use App\Models\CaseAssistance;
use App\Models\CharityCase;

/**
 * The simple totals the dashboard shows for charity cases and their
 * assistances. Installment states follow InstallmentStatusEnum: paid when
 * `paid_at` is set, cancelled when `cancelled_at` is set, otherwise pending,
 * and overdue once a pending one's due date is before today.
 */
class CharityStatisticsService
{
    /**
     * @return array{total: int, with_assistances: int, without_assistances: int}
     */
    public function cases(): array
    {
        $total = CharityCase::query()->count();
        $withAssistances = CharityCase::query()->has('assistances')->count();

        return [
            'total' => $total,
            'with_assistances' => $withAssistances,
            'without_assistances' => $total - $withAssistances,
        ];
    }

    /**
     * @return array{total: int, active: int, one_time: int, recurring: int, paid_amount: float, remaining_amount: float, overdue_installments: int}
     */
    public function assistances(): array
    {
        $byType = CaseAssistance::query()
            ->selectRaw('assistance_type, count(*) as total')
            ->groupBy('assistance_type')
            ->pluck('total', 'assistance_type');

        $pending = AssistanceInstallment::query()->whereNull('paid_at')->whereNull('cancelled_at');

        return [
            'total' => (int) $byType->sum(),
            'active' => CaseAssistance::query()->where('active', true)->count(),
            'one_time' => (int) ($byType[AssistanceTypeEnum::OneTime->value] ?? 0),
            'recurring' => (int) ($byType[AssistanceTypeEnum::Recurring->value] ?? 0),
            'paid_amount' => (float) AssistanceInstallment::query()->whereNotNull('paid_at')->sum('amount'),
            'remaining_amount' => (float) (clone $pending)->sum('amount'),
            'overdue_installments' => (clone $pending)->whereDate('due_at', '<', today())->count(),
        ];
    }
}

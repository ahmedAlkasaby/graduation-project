<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

class CaseAssistanceBuilder extends BaseBuilder
{
    /**
     * `search` looks in the title and in the beneficiary's name, national ID
     * and phone, so an admin can find a plan by who it is for.
     *
     * @param  array{search?: string, case_id?: int|string, category_id?: int|string, assistance_type?: string}  $filters
     */
    public function filter(array $filters): static
    {
        $term = $filters['search'] ?? null;

        return $this
            ->when(filled($term), fn (Builder $query) => $query->where(function (Builder $query) use ($term): void {
                $query->where('title', 'like', "%{$term}%")
                    ->orWhereHas('charityCase', fn (Builder $case) => $case->search($term));
            }))
            ->when($filters['case_id'] ?? null, fn (Builder $query, $caseId) => $query->where('case_id', $caseId))
            ->when($filters['category_id'] ?? null, fn (Builder $query, $categoryId) => $query->whereHas(
                'charityCase',
                fn (Builder $case) => $case->where('category_id', $categoryId),
            ))
            ->when($filters['assistance_type'] ?? null, fn (Builder $query, $type) => $query->where('assistance_type', $type))
            ->defaultOrder();
    }

    /** Installment counts and the next unpaid due date, for list rows. */
    public function withInstallmentSummary(): static
    {
        return $this->withCount([
            'installments',
            'installments as paid_installments_count' => fn (Builder $query) => $query->whereNotNull('paid_at'),
        ])->withMin(
            ['installments as next_due_at' => fn (Builder $query) => $query->whereNull('paid_at')->whereNull('cancelled_at')],
            'due_at',
        );
    }
}

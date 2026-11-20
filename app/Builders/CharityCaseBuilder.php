<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

class CharityCaseBuilder extends BaseBuilder
{
    /**
     * @param  array{search?: string, category_id?: int|string, volunteer_id?: int|string}  $filters
     */
    public function filter(array $filters): static
    {
        return $this->search($filters['search'] ?? null)
            ->when($filters['category_id'] ?? null, fn (Builder $query, $categoryId) => $query->where('category_id', $categoryId))
            ->when($filters['volunteer_id'] ?? null, fn (Builder $query, $volunteerId) => $query->where('volunteer_id', $volunteerId))
            ->latest('id');
    }
}

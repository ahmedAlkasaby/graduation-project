<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

class CaseCategoryBuilder extends BaseBuilder
{
    /**
     * @param  array{search?: string, active?: bool|string}  $filters
     */
    public function filter(array $filters): static
    {
        return $this->search($filters['search'] ?? null)
            ->when(isset($filters['active']), fn (Builder $query) => $query->active((bool) $filters['active']))
            ->defaultOrder();
    }
}

<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

class RegionBuilder extends BaseBuilder
{
    /**
     * @param  array{search?: string, city_id?: int|string, active?: bool|string}  $filters
     */
    public function filter(array $filters): static
    {
        return $this->search($filters['search'] ?? null)
            ->when($filters['city_id'] ?? null, fn (Builder $query, $cityId) => $query->where('city_id', $cityId))
            ->when(isset($filters['active']), fn (Builder $query) => $query->active((bool) $filters['active']))
            ->defaultOrder();
    }
}

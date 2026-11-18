<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

class CityBuilder extends BaseBuilder
{
    /**
     * @param  array{search?: string, country_id?: int|string, active?: bool|string}  $filters
     */
    public function filter(array $filters): static
    {
        return $this->search($filters['search'] ?? null)
            ->when($filters['country_id'] ?? null, fn (Builder $query, $countryId) => $query->where('country_id', $countryId))
            ->when(isset($filters['active']), fn (Builder $query) => $query->active((bool) $filters['active']))
            ->defaultOrder();
    }
}

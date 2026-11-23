<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

class CharityCaseBuilder extends BaseBuilder
{
    /**
     * `city_id` and `region_id` match the beneficiary's address.
     *
     * @param  array{search?: string, volunteer_id?: int|string, city_id?: int|string, region_id?: int|string}  $filters
     */
    public function filter(array $filters): static
    {
        return $this->search($filters['search'] ?? null)
            ->when($filters['volunteer_id'] ?? null, fn (Builder $query, $volunteerId) => $query->where('volunteer_id', $volunteerId))
            ->when($filters['city_id'] ?? null, fn (Builder $query, $cityId) => $query->whereHas('address', fn (Builder $address) => $address->where('city_id', $cityId)))
            ->when($filters['region_id'] ?? null, fn (Builder $query, $regionId) => $query->whereHas('address', fn (Builder $address) => $address->where('region_id', $regionId)))
            ->latest('id');
    }
}

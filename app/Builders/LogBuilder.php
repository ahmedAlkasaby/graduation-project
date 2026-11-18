<?php

namespace App\Builders;

use App\Builders\Concerns\FiltersLogs;
use Illuminate\Database\Eloquent\Builder;

class LogBuilder extends BaseBuilder
{
    use FiltersLogs;

    /**
     * @param  array<string, mixed>  $filters  validated LogRequest data
     */
    public function filter(array $filters): static
    {
        return $this->when($filters['model'] ?? null, fn (Builder $query, $model) => $query->where('loggable_type', $model))
            ->when($filters['field_id'] ?? null, fn (Builder $query, $id) => $query->where('loggable_id', $id))
            ->when($filters['key'] ?? null, fn (Builder $query, $key) => $query->where('key', $key))
            ->filterUser($filters['user_id'] ?? null)
            ->filterDevice($filters)
            ->createdBetween($filters['date_start'] ?? null, $filters['date_end'] ?? null);
    }
}

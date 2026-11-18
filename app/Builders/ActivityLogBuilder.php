<?php

namespace App\Builders;

use App\Builders\Concerns\FiltersLogs;
use Illuminate\Database\Eloquent\Builder;

class ActivityLogBuilder extends BaseBuilder
{
    use FiltersLogs;

    /**
     * @param  array<string, mixed>  $filters  validated ActivityLogRequest data
     */
    public function filter(array $filters): static
    {
        return $this->when($filters['action'] ?? null, fn (Builder $query, $action) => $query->where('action', logActionName($action)))
            ->when($filters['route'] ?? null, fn (Builder $query, $route) => $query->where('route', 'like', "%{$route}%"))
            ->when($filters['model'] ?? null, fn (Builder $query, $model) => $query->where('activity_loggable_type', $model))
            ->when($filters['field_id'] ?? null, fn (Builder $query, $id) => $query->where('activity_loggable_id', $id))
            ->when($filters['type'] ?? null, fn (Builder $query, $type) => $query->where('type', $type))
            ->filterUser($filters['user_id'] ?? null)
            ->filterDevice($filters)
            ->createdBetween($filters['date_start'] ?? null, $filters['date_end'] ?? null);
    }
}

<?php

namespace App\Builders\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait FiltersLogs
{
    /** @param  array<int, int|string>|null  $userIds */
    protected function filterUser(?array $userIds): static
    {
        return empty($userIds) ? $this : $this->whereIn('user_id', $userIds);
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    protected function filterDevice(array $filters): static
    {
        $matches = array_filter(
            [
                'device' => $filters['device'] ?? null,
                'platform' => $filters['platform'] ?? null,
                'browser' => $filters['browser'] ?? null,
            ],
            fn ($value) => filled($value),
        );

        $flags = array_filter(
            [
                'is_desktop' => $filters['is_desktop'] ?? null,
                'is_phone' => $filters['is_phone'] ?? null,
            ],
            fn ($value) => filled($value),
        );

        return $this->when($filters['user_device_id'] ?? null, fn (Builder $query, $id) => $query->where('user_device_id', $id))
            ->when($matches !== [] || $flags !== [], fn (Builder $query) => $query->whereHas('userDevice', function (Builder $device) use ($matches, $flags): void {
                foreach ($matches as $column => $value) {
                    $device->where($column, 'like', "%{$value}%");
                }

                foreach ($flags as $column => $value) {
                    $device->where($column, (int) $value);
                }
            }));
    }
}

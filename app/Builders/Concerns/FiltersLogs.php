<?php

namespace App\Builders\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait FiltersLogs
{
    protected function filterUser($request): static
    {
        $user_id = $request->input('user_id');

        if (empty($user_id)) {
            return $this;
        }

        return $this->whereIn('user_id', array_filter((array) $user_id, fn ($id) => $id !== '' && $id !== null));
    }

    protected function filterDevice($request): static
    {
        if ($request->filled('user_device_id')) {
            $this->where('user_device_id', $request->input('user_device_id'));
        }

        $matches = array_filter([
            'device' => $request->input('device'),
            'platform' => $request->input('platform'),
            'browser' => $request->input('browser'),
        ], fn ($value) => $value !== null && $value !== '');

        $flags = array_filter([
            'is_desktop' => $request->input('is_desktop'),
            'is_phone' => $request->input('is_phone'),
        ], fn ($value) => $value !== null && $value !== '');

        if ($matches === [] && $flags === []) {
            return $this;
        }

        return $this->whereHas('userDevice', function (Builder $query) use ($matches, $flags) {
            foreach ($matches as $column => $value) {
                $query->where($column, 'like', '%'.$value.'%');
            }

            foreach ($flags as $column => $value) {
                $query->where($column, (int) $value);
            }
        });
    }
}

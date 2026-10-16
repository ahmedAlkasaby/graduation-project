<?php

namespace App\Builders;

use App\Builders\Concerns\FiltersLogs;

class LogBuilder extends BaseBuilder
{
    use FiltersLogs;

    public function filter($request = null): static
    {
        $request = $request ?? request();

        if ($request->filled('model')) {
            $this->where('loggable_type', $request->input('model'));
        }

        if ($request->filled('field_id')) {
            $this->where('loggable_id', $request->input('field_id'));
        }

        if ($request->filled('key')) {
            $this->where('key', $request->input('key'));
        }

        return $this->filterUser($request)
            ->filterDevice($request)
            ->dateFilter($request);
    }
}

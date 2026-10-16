<?php

namespace App\Builders;

use App\Builders\Concerns\FiltersLogs;

class ActivityLogBuilder extends BaseBuilder
{
    use FiltersLogs;

    public function filter($request = null): static
    {
        $request = $request ?? request();

        if ($request->filled('action')) {
            $this->where('action', logActionName($request->input('action')));
        }

        if ($request->filled('route')) {
            $this->where('route', 'like', '%'.$request->input('route').'%');
        }

        if ($request->filled('model')) {
            $this->where('activity_loggable_type', $request->input('model'));
        }

        if ($request->filled('field_id')) {
            $this->where('activity_loggable_id', $request->input('field_id'));
        }

        if ($request->filled('type')) {
            $this->where('type', $request->input('type'));
        }

        return $this->filterUser($request)
            ->filterDevice($request)
            ->dateFilter($request);
    }
}

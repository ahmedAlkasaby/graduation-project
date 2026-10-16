<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\ActivityLogRequest;
use App\Models\ActivityLog;
use App\Models\User;


class ActivityLogController extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->setClass('activity_log');
        $this->table = 'activity_logs';
    }

    public function index(ActivityLogRequest $request)
    {
        return view('admin.activity_logs.index', [
            'data' => ActivityLog::with(['user', 'userDevice'])
                ->filter($request)
                ->orderByDesc('id')
                ->paginateRequested($request, $this->limit),
            'users' => $this->filterUsers(),
            'models' => $this->filterModels(),
        ]);
    }

    protected function filterUsers(): array
    {
        return User::query()
            ->withTrashed()
            ->whereIn('id', ActivityLog::query()->distinct()->pluck('user_id'))
            ->pluck('name', 'id')
            ->toArray();
    }

    protected function filterModels(): array
    {
        return ActivityLog::query()
            ->distinct()
            ->whereNotNull('activity_loggable_type')
            ->pluck('activity_loggable_type')
            ->mapWithKeys(fn ($table) => [$table => getModuleTableModelName($table)])
            ->prepend(__('All'), '')
            ->toArray();
    }
}

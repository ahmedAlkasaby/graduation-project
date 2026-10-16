<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\LogRequest;
use App\Models\Log;
use App\Models\User;


class LogController extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->setClass('log');
        $this->table = 'logs';
    }

    public function index(LogRequest $request)
    {
        return view('admin.logs.index', [
            'data' => Log::with(['user', 'userDevice'])
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
            ->whereIn('id', Log::query()->distinct()->pluck('user_id'))
            ->pluck('name', 'id')
            ->toArray();
    }

    protected function filterModels(): array
    {
        return Log::query()
            ->distinct()
            ->whereNotNull('loggable_type')
            ->pluck('loggable_type')
            ->mapWithKeys(fn ($table) => [$table => getModuleTableModelName($table)])
            ->prepend(__('All'), '')
            ->toArray();
    }
}

<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Traits\AdminHelperTrait;
use App\Traits\AgentTrait;
use Illuminate\Http\Request;

class ActivityLogger
{
    use AdminHelperTrait, AgentTrait;

    protected array $skip_modules = ['theme', 'lang', 'profile', 'notifications'];

    protected array $skip_keywords = ['module', 'optimize', 'clear-cache'];

    protected array $log_single_segment = ['statistics', 'reports', 'analytics', 'dashboard'];

    protected array $device_cache = [];

    public function record(Request $request, $user, string $type = 'admin'): void
    {
        if (! $user || in_array((int) $user->id, $this->access_all_id, true)) {
            return;
        }

        $route = $request->route();
        $name = $route?->getName();

        if (! $name) {
            return;
        }

        $segments = explode('.', str_replace([$type.'.', 'admin.', 'api.'], '', $name));
        $module = $segments[0];

        if (! $this->shouldLog($name, $module, count($segments))) {
            return;
        }

        ActivityLog::create([
            'user_id' => $user->id,
            'user_device_id' => $this->deviceFor($user, $type),
            'activity_loggable_id' => $this->resourceId($request),
            'activity_loggable_type' => $module,
            'type' => $type,
            'status' => end($segments),
            'action' => $route->methods()[0],
            'route' => str_replace('/public', '', $request->getRequestUri()),
        ]);
    }

    protected function shouldLog(string $name, string $module, int $segment_count): bool
    {
        if (in_array($module, $this->skip_modules, true)) {
            return false;
        }

        foreach ($this->skip_keywords as $keyword) {
            if (str_contains($name, $keyword)) {
                return false;
            }
        }

        // "users.index" is a module action; a bare "unauthorized" is not.
        return $segment_count > 1 || in_array($module, $this->log_single_segment, true);
    }

    protected function resourceId(Request $request): ?int
    {
        foreach ($request->route()?->parameters() ?? [] as $value) {
            if (is_numeric($value)) {
                return (int) $value;
            }

            if (is_object($value) && method_exists($value, 'getKey') && is_numeric($value->getKey())) {
                return (int) $value->getKey();
            }
        }

        $id = (int) ($request->segment(3) ?: $request->segment(4));

        return $id > 0 ? $id : null;
    }

    protected function deviceFor($user, string $type): ?int
    {
        if (! array_key_exists($user->id, $this->device_cache)) {
            $this->device_cache[$user->id] = $this->checkAgent($type === 'api' ? 'sanctum' : 'web', $user);
        }

        return $this->device_cache[$user->id];
    }
}

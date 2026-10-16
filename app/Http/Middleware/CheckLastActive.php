<?php

namespace App\Http\Middleware;

use App\Services\ActivityLogger;
use Carbon\Carbon;
use Closure;

/**
 * API side: keeps `last_active` current and records the request in
 * `activity_logs`. Sits on the authenticated api group.
 */
class CheckLastActive
{
    public function __construct(protected ActivityLogger $logger) {}

    public function handle($request, Closure $next, $guard = null)
    {
        $user = auth('sanctum')->user()
            ?: ($guard && auth()->guard($guard)->check() ? auth($guard)->user() : auth()->user());

        if ($user) {
            $user->forceFill(['last_active' => Carbon::now()])->saveQuietly();

            $this->logger->record($request, $user, 'api');
        }

        return $next($request);
    }
}

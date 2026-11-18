<?php

namespace App\Http\Middleware;

use App\Services\ActivityLogger;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * For the signed in user, admin or app user alike: blocks a deactivated
 * account, keeps `last_active` current and records the request in
 * `activity_logs`, typed "admin" or "api" by the user's type.
 */
class CheckLastActive
{
    public function __construct(protected ActivityLogger $logger) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('sanctum');

        if (! $user) {
            return $next($request);
        }

        if (! $user->active) {
            return response()->json([
                'status' => 'error',
                'message' => __('Your account is not active'),
            ], Response::HTTP_FORBIDDEN);
        }

        $user->forceFill(['last_active' => Carbon::now()])->saveQuietly();

        $this->logger->record($request, $user, $user->isAdmin() ? 'admin' : 'api');

        return $next($request);
    }
}

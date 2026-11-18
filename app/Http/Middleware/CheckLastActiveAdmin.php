<?php

namespace App\Http\Middleware;

use App\Services\ActivityLogger;
use Carbon\Carbon;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin API side: blocks deactivated admins, keeps `last_active` current and
 * records the request in `activity_logs`. Used as "check.lastactive.admin:sanctum".
 */
class CheckLastActiveAdmin
{
    public function __construct(protected ActivityLogger $logger) {}

    public function handle(Request $request, Closure $next, ?string $guard = 'sanctum'): Response
    {
        $user = auth($guard)->user();

        if (! $user) {
            throw new AuthenticationException;
        }

        if (! $user->active) {
            return response()->json([
                'status' => 'error',
                'message' => __('Your account is not active'),
            ], Response::HTTP_FORBIDDEN);
        }

        $user->forceFill(['last_active' => Carbon::now()])->saveQuietly();

        $this->logger->record($request, $user, 'admin');

        return $next($request);
    }
}

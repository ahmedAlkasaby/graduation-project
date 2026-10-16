<?php

namespace App\Http\Middleware;

use App\Services\ActivityLogger;
use Carbon\Carbon;
use Closure;
use Illuminate\Auth\AuthenticationException;


class CheckLastActiveAdmin
{
    public function __construct(protected ActivityLogger $logger) {}

    public function handle($request, Closure $next, $guard = null)
    {
        $user = auth($guard)->user();

        if (! $user) {
            throw new AuthenticationException;
        }

        $user->forceFill(['last_active' => Carbon::now()])->saveQuietly();

        $this->logger->record($request, $user, 'admin');

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use App\Services\ActivityLogger;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Auth;


class CheckLastActiveAdmin
{
    public function __construct(protected ActivityLogger $logger) {}

    public function handle($request, Closure $next, $guard = null)
    {
        $user = auth($guard)->user();

        if (! $user) {
            return redirect()->route('admin.login');
        }

        if (! $user->active) {
            Auth::logout();

            return redirect()->route('admin.login')->with('error', __('Your account is not active'));
        }

        $user->forceFill(['last_active' => Carbon::now()])->saveQuietly();

        $this->logger->record($request, $user, 'admin');

        return $next($request);
    }
}

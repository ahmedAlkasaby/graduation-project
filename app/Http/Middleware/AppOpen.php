<?php

namespace App\Http\Middleware;

use App\Traits\AdminHelperTrait;
use Closure;
use Illuminate\Http\Request;

/**
 * Honours the "app_open" setting: while the app is closed only owners get through.
 */
class AppOpen
{
    use AdminHelperTrait;

    public function handle(Request $request, Closure $next)
    {
        if (settings()->isAppOpen()) {
            return $next($request);
        }

        $user = auth()->user();

        if ($user && in_array($user->type, $this->access_all_type)) {
            return $next($request);
        }

        return response()->json([
            'status' => false,
            'message' => __('App is closed'),
        ], 503);
    }
}

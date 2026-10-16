<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restricts an API endpoint to admin accounts — everything the dashboard
 * calls sits behind this. Donors and volunteers get a 403.
 */
class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('sanctum')->user() ?? $request->user();

        if (! $user || ! $user->isAdmin()) {
            return response()->json([
                'status' => 'unauthorized',
                'message' => __("You don't have permission to do this"),
            ], 403);
        }

        return $next($request);
    }
}

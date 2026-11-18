<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Answers 503 while the site is closed. A signed in admin still gets through,
 * so the dashboard keeps working on the shared public endpoints.
 */
class SiteOpen
{
    public function handle(Request $request, Closure $next): Response
    {
        if (settings()->isSiteOpen() || auth('sanctum')->user()?->isAdmin()) {
            return $next($request);
        }

        return response()->json([
            'status' => 'error',
            'message' => __('Site is closed'),
        ], Response::HTTP_SERVICE_UNAVAILABLE);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class SiteOpen
{
    public function handle(Request $request, Closure $next): Response
    {
        if (settings()->isSiteOpen()) {
            return $next($request);
        }
        return response()->json([
            'status' => 'error',
            'message' => __('Site is closed'),
        ], Response::HTTP_SERVICE_UNAVAILABLE);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restricts an API endpoint to client accounts.
 *
 * Deleting your own account or editing your own address is a client action;
 * an admin does the equivalent through the panel, against a chosen user.
 */
class EnsureUserIsClient
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('sanctum')->user() ?? $request->user();

        if (! $user || ! $user->isClient()) {
            return response()->json([
                'status' => 'unauthorized',
                'message' => __("You don't have permission to do this"),
            ], 403);
        }

        return $next($request);
    }
}

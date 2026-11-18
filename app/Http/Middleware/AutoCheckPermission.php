<?php

namespace App\Http\Middleware;

use App\Models\Permission;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Checks the dashboard permission of the current admin route.
 *
 *  - Permissions passed to the middleware are used as given:
 *    `->permission('cities.export')` (any one of several is enough).
 *  - Otherwise the permission is taken from the route name:
 *    "api.v1.admin.cities.index" needs "cities.index".
 *
 * A super admin skips the check. A route with no permission is refused.
 */
class AutoCheckPermission
{
    public const ALIAS = 'auto-check-permission';

    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user('sanctum');

        if ($user?->isSuperAdmin()) {
            return $next($request);
        }

        if ($permissions === []) {
            $permissions = array_filter([Permission::nameForRoute(static::permissionRouteName($request->route()->getName()))]);
        }

        if ( $permissions === [] || ! $user->hasPermission($permissions)) {
            throw new AuthorizationException;
        }

        return $next($request);
    }

    /** "api.v1.admin.cities.index" becomes "cities.index". */
    public static function permissionRouteName(?string $routeName): string
    {
        return (string) preg_replace('/^(api\.v\d+\.)?admin\./', '', (string) $routeName);
    }
}

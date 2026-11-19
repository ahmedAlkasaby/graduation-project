<?php

namespace App\Http\Middleware;

use App\Models\Permission;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Checks the dashboard permission of the current admin route, for the API
 * and the Blade admin panel alike.
 *
 *  - Permissions passed to the middleware are used as given:
 *    `->permission('cities.export')` (any one of several is enough).
 *  - Otherwise the permission is taken from the route name:
 *    "api.v1.admin.cities.index" and "admin.cities.index" need "cities.index".
 *    The panel's create and edit screens need the store and update permission.
 *
 * A super admin skips the check. A route with no permission is refused.
 */
class AutoCheckPermission
{
    public const ALIAS = 'auto-check-permission';

    /** Blade screens that only show a form, and the action that saves it. */
    private const FORM_ACTIONS = [
        'create' => 'store',
        'edit' => 'update',
    ];

    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user('sanctum') ?? $request->user();

        if ($user?->isSuperAdmin()) {
            return $next($request);
        }

        if ($permissions === []) {
            $permissions = array_filter([Permission::nameForRoute(static::permissionRouteName($request->route()->getName()))]);
        }

        if ($permissions === [] || ! $user?->hasPermission($permissions)) {
            // The Blade panel shows its "no access" page; the API answers 403.
            if ($request->routeIs('admin.*') && ! $request->expectsJson()) {
                return redirect()->route('admin.unauthorized');
            }

            throw new AuthorizationException;
        }

        return $next($request);
    }

    /** "api.v1.admin.cities.index" becomes "cities.index", "admin.cities.edit" becomes "cities.update". */
    public static function permissionRouteName(?string $routeName): string
    {
        $name = (string) preg_replace('/^(api\.v\d+\.)?admin\./', '', (string) $routeName);

        $module = Str::beforeLast($name, '.');
        $action = Str::afterLast($name, '.');

        if ($module !== $name && isset(self::FORM_ACTIONS[$action])) {
            return $module.'.'.self::FORM_ACTIONS[$action];
        }

        return $name;
    }
}

<?php

namespace App\Http\Middleware;

use App\Models\Permission;
use App\Traits\AdminHelperTrait;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;

/**
 * Every admin route is named after its module and action ("users.edit"), and
 * every permission is named after the routes it covers. So instead of hanging
 * a permission check on each route, this looks the current route name up and
 * enforces whatever permission claims it.
 *
 * A route with no matching permission is public within the panel.
 */
class AutoCheckPermission
{
    use AdminHelperTrait;

    public function handle($request, Closure $next)
    {
        $user = auth('sanctum')->user();

        if ($user && in_array($user->type, $this->access_all_type)) {
            return $next($request);
        }

        $route = str_replace('admin.', '', (string) $request->route()?->getName());

        $permission = Permission::nameForRoute($route);

        if ($permission !== null && ! $user?->can($permission)) {
            throw new AuthorizationException;
        }

        return $next($request);
    }
}

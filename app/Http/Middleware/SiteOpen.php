<?php

namespace App\Http\Middleware;

use App\Traits\AdminHelperTrait;
use Closure;

/**
 * Honours the "site_open" setting. Owners keep working while the site is
 * closed so the switch can be turned back on.
 */
class SiteOpen
{
    use AdminHelperTrait;

    public function handle($request, Closure $next, $guard = null)
    {
        if (settings()->isSiteOpen()) {
            return $next($request);
        }

        $user = auth($guard)->user();

        if ($user && in_array($user->type, $this->access_all_type)) {
            return $next($request);
        }

        return redirect()->route('close');
    }
}

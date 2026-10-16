<?php

namespace App\Http\Middleware;

use App\Traits\AdminHelperTrait;
use Closure;
use Illuminate\Support\Facades\Auth;

/**
 * Lets a signed in user into the admin panel only if they hold at least one
 * panel permission.
 */
class AdminPanel
{
    use AdminHelperTrait;

    public function handle($request, Closure $next, $guard = null)
    {
        if (! Auth::guard($guard)->check()) {
            return redirect()->route('admin.login');
        }

        $user = auth($guard)->user();

        if (! in_array($user->type, $this->access_all_type)
            && ! $user->isAbleTo($this->admin_perms_models)) {
            return redirect()->route('admin.unauthorized');
        }

        return $next($request);
    }
}

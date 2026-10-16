<?php

namespace App\Http\Middleware;

use App\Traits\AdminHelperTrait;
use Closure;
use Illuminate\Support\Facades\Auth;

/**
 * Guards the translation manager UI.
 */
class AdminPanelTrans
{
    use AdminHelperTrait;

    public function handle($request, Closure $next, $guard = null)
    {
        if (! Auth::guard($guard)->check()) {
            return redirect()->route('admin.login');
        }

        $user = auth($guard)->user();

        if (! in_array($user->type, $this->access_all_type)
            && ! $user->can('translations.index')) {
            return redirect()->route('admin.unauthorized');
        }

        return $next($request);
    }
}

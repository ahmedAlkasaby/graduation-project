<?php

namespace App\Http\Middleware;

use App\Traits\AdminHelperTrait;
use Closure;

/**
 * Guards the parts of the panel only an owner should reach — settings,
 * developer tools, the destructive log actions.
 */
class AccessAll
{
    use AdminHelperTrait;

    public function handle($request, Closure $next)
    {
        $user = auth()->user();

        if (! $user || ! in_array($user->type, $this->access_all_type)) {
            return redirect()->route('admin.unauthorized');
        }

        return $next($request);
    }
}

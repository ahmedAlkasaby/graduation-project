<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSocialLoginAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (settings()->get('social_login') !== 'yes') {
            return redirect()->route('admin.unauthorized');
        }

        return $next($request);
    }
}

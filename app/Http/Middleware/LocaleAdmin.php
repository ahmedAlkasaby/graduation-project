<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Same as Locale, but the default comes from the `admin_language` setting.
 */
class LocaleAdmin
{
    protected $languages = ['ar', 'en'];

    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $lang = settings()->get('admin_language', app()->getLocale());

        if ($request->header('lang') && in_array($request->header('lang'), $this->languages)) {
            $lang = $request->header('lang');
        }

        $auth = auth('sanctum')->user();
        if ($auth && $auth->locale != $lang) {
            $lang = $auth->locale;
        }

        app()->setLocale($lang);

        return $next($request);
    }
}

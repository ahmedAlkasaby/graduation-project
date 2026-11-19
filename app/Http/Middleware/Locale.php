<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sets the request language: the signed in user's own locale, else a valid
 * `lang` header, else the default setting — `admin_language` on admin routes,
 * `site_language` everywhere else.
 */
class Locale
{
    protected array $languages = ['ar', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        $setting = $request->routeIs('api.*.admin.*', 'admin.*') ? 'admin_language' : 'site_language';

        $lang = settings()->get($setting, app()->getLocale());

        if (in_array($request->header('lang'), $this->languages, true)) {
            $lang = $request->header('lang');
        }

        if ($user = $request->user('sanctum')) {
            $lang = $user->locale;
        }

        app()->setLocale($lang);

        return $next($request);
    }
}

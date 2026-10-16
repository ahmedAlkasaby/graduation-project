<?php

namespace App\Http\Middleware;

use Closure;
// use Illuminate\Support\Facades\Auth;
use Request;
class LocaleSite {

    /**
     * The availables languages.
     *
     * @array $languages
     */
    protected $languages = ['ar','en'];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null) {
        $site_language = settings()->get('site_language');
        $language = ( in_array( Request::segment(1), $this->languages) ) ? Request::segment(1) : $site_language;
        app()->setLocale($language);
        return $next($request);
    }

}

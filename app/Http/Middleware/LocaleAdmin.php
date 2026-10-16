<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class LocaleAdmin {
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

        if(auth('sanctum')->user()){
            $user = auth('sanctum')->user();
            app()->setLocale($user->locale);
        }elseif(auth()->guard($guard)->check()){
            $user = auth($guard)->user();
            app()->setLocale($user->locale);
        }else{
            $admin_language = settings()->get('admin_language');
            $language = ( in_array( $admin_language, $this->languages) ) ? $admin_language : "ar";
            app()->setLocale($language);
        }
        return $next($request);
    }

}

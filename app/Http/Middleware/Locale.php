<?php

namespace App\Http\Middleware;


use Closure;
use Illuminate\Http\Request;

class Locale {

   
    protected $languages = ['ar','en'];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $lang=settings()->get('site_language',app()->getLocale());

        if ($request->header('lang') && in_array($request->header('lang'), $this->languages)) {
           $lang = $request->header('lang');
        }

        $auth=auth('sanctum')->user();
        if($auth && $auth->locale != $lang){
            $lang=$auth->locale;
        }

        app()->setLocale($lang);

        return $next($request);
    }
   

}

<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        Route::pattern('id', '[0-9]+');
        Route::pattern('hash', '[a-z0-9]+');
        Route::pattern('hex', '[a-f0-9]+');
        Route::pattern('uuid', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');
        Route::pattern('base', '[a-zA-Z0-9]+');
        Route::pattern('slug', '[a-zA-Z0-9_-]+');
        Route::pattern('username', '[a-z0-9_-]{3,25}');

        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::prefix('api')->middleware('api')
                ->group(base_path('routes/api.php'));
        });
        parent::boot();

    }


    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });


        RateLimiter::for('login', function (Request $request) {
            $identifier = (string) ($request->input('email') ?: $request->input('phone') ?: '');

            return [
                Limit::perMinute(5)->by('login:'.$request->ip()),
                Limit::perMinute(5)->by('login:'.mb_strtolower($identifier)),
            ];
        });

        RateLimiter::for('register', function (Request $request) {
            return Limit::perHour(10)->by('register:'.$request->ip());
        });


        RateLimiter::for('sms', function (Request $request) {
            $phone = (string) $request->input('phone', '');

            return [
                Limit::perMinute(3)->by('sms:'.$request->ip()),
                Limit::perHour(10)->by('sms:'.$request->ip()),
                Limit::perHour(5)->by('sms-phone:'.$phone),
            ];
        });

        RateLimiter::for('otp', function (Request $request) {
            $identifier = (string) ($request->input('email') ?: $request->input('phone') ?: '');

            return [
                Limit::perMinute(5)->by('otp:'.$request->ip()),
                Limit::perHour(15)->by('otp:'.mb_strtolower($identifier)),
            ];
        });
    }
}

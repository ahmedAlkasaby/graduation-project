<?php

use App\Enums\TokenAbilityEnum;
use App\Http\Controllers\Api\V1\Admin\Auth\AuthController as AdminAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Every endpoint is versioned (/api/v1/...) and lives in one of two contexts:
|
|  - admin  : dashboard management. Every route is behind the admin guard and
|            the permission named after the route ("admin.cities.index"
|            needs "cities.index").
|  - public : everything else — guests, signed in app users, and the
|            self-service endpoints an admin uses as a regular user
|            (sign in, own profile, own notifications). No permissions.
|
| Controllers mirror this layout: App\Http\Controllers\Api\V1\{Admin,Public}.
| A new version gets its own route folder (routes/api/v2), controller folder
| (Api\V2) and block here; existing versions are left untouched.
|
*/

Route::middleware(['cors', 'json.response', 'locale'])->group(function () {

    Route::prefix('v1')->as('api.v1.')->group(function () {

        Route::prefix('admin')->as('admin.')->group(function () {

            // The one admin route a guest can reach: it hands out the token the rest need.
            Route::post('login', [AdminAuthController::class, 'login'])->name('login')->middleware('throttle:login');

            Route::middleware([
                'auth:sanctum',
                'abilities:'.TokenAbilityEnum::AccessToken->value,
                'admin',
                'check.lastactive',
                'auto-check-permission',
            ])->group(base_path('routes/api/v1/admin.php'));
        });

        Route::prefix('public')
            ->as('public.')
            ->group(base_path('routes/api/v1/public.php'));
    });
});

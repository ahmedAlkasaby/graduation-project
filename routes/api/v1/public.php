<?php

use App\Enums\TokenAbilityEnum;
use App\Http\Controllers\Api\V1\Public\ApiController;
use App\Http\Controllers\Api\V1\Public\Auth\AuthController;
use App\Http\Controllers\Api\V1\Public\Auth\ForgetPasswordController;
use App\Http\Controllers\Api\V1\Public\Auth\SocialLoginController;
use App\Http\Controllers\Api\V1\Public\CityController;
use App\Http\Controllers\Api\V1\Public\NotificationController;
use App\Http\Controllers\Api\V1\Public\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API v1 — Public
|--------------------------------------------------------------------------
|
| Served under /api/v1/public, named "api.v1.public.*".
|
| Guests, app users and admins all use these. Nothing here is checked against
| a dashboard permission: a route that needs one belongs in admin.php.
|
| Signing in, refreshing a token and reading the app settings stay reachable
| while the site is closed, so an admin can still get in and reopen it.
|
*/

Route::post('login', [AuthController::class, 'login'])->name('login')->middleware('throttle:login');
Route::post('refresh', [AuthController::class, 'refresh'])->name('refresh')
    ->middleware(['auth:sanctum', 'abilities:'.TokenAbilityEnum::RefreshToken->value, 'check.lastactive']);
Route::get('settings', [ApiController::class, 'settings'])->name('settings');

Route::middleware(['site.open'])->group(function () {

    Route::get('social_login', [SocialLoginController::class, 'tokenLogin'])->name('social.login')
        ->middleware('check.socialLogin');

    Route::group(['prefix' => 'register', 'as' => 'register.'], function () {
        Route::post('', [AuthController::class, 'register'])->name('store')->middleware('throttle:register');
        Route::post('check', [AuthController::class, 'checkUser'])->name('check')->middleware('throttle:sms');
    });

    Route::group(['prefix' => 'password', 'as' => 'password.'], function () {
        Route::post('forget', [ForgetPasswordController::class, 'forget'])->name('forget')->middleware('throttle:sms');
        Route::post('reset', [ForgetPasswordController::class, 'reset'])->name('reset')->middleware('throttle:otp');
    });

    Route::middleware(['check.lastactive'])->group(function () {

        Route::apiResource('cities', CityController::class)->only(['index', 'show']);

        Route::middleware(['auth:sanctum', 'abilities:'.TokenAbilityEnum::AccessToken->value])->group(function () {

            Route::group(['prefix' => 'profile/change', 'as' => 'profile.change.'], function () {
                Route::post('image', [ProfileController::class, 'changeImage'])->name('image');
                Route::post('password', [ProfileController::class, 'changePassword'])->name('password');
                Route::post('lang', [ProfileController::class, 'changeLang'])->name('lang');
            });

            Route::post('logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('profile/permissions', [ProfileController::class, 'permissions'])->name('profile.permissions');
            Route::post('profile/settings', [ProfileController::class, 'settings'])->name('profile.settings');
            Route::post('profile/delete', [ProfileController::class, 'delete'])->name('profile.deleteAccount');
            Route::apiResource('profile', ProfileController::class)->except(['store']);

            Route::apiResource('notifications', NotificationController::class)->except(['store']);
        });
    });
});

<?php

use App\Enums\TokenAbilityEnum;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\ForgetPasswordController;
use App\Http\Controllers\Api\Auth\SocialLoginController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['locale', 'cors', 'json.response', 'site.open'],
    'as' => 'api.',
], function () {

    Route::middleware(['check.socialLogin'])->group(function () {
        Route::get('social_login', [SocialLoginController::class, 'tokenLogin'])->name('social.login');
    });

    Route::post('login', [AuthController::class, 'login'])->name('login')->middleware('throttle:login');
    Route::post('refresh', [AuthController::class, 'refresh'])->name('refresh')
        ->middleware(['auth:sanctum', 'abilities:'.TokenAbilityEnum::RefreshToken->value, 'check.lastactive']);
    Route::get('settings', [ApiController::class, 'settings'])->name('settings')->withoutMiddleware('site.open');

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

            Route::post('logout', [AuthController::class, 'logout'])->name('logout.token');
            Route::post('profile/settings', [ProfileController::class, 'settings'])->name('profile.settings');
            Route::post('profile/delete', [ProfileController::class, 'delete'])->name('profile.deleteAccount');
            Route::apiResource('profile', ProfileController::class)->except(['store']);

            Route::apiResource('notifications', NotificationController::class)->except(['store']);

        });
    });
});

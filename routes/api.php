<?php

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\Auth\PasswordResetController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\Auth\SmsController;
use App\Http\Controllers\Api\Auth\SocialLoginController;
use Illuminate\Support\Facades\Route;



Route::group([
    'middleware' => ['locale', 'cors', 'json.response'],
    'as' => 'api.',
], function () {

    Route::middleware(['check.socialLogin'])->group(function () {
        Route::get('social_login', [SocialLoginController::class, 'tokenLogin'])->name('social.login');
    });

    Route::post('login', [AuthController::class, 'login'])->name('login')->middleware('throttle:login');
    Route::get('settings', [ApiController::class, 'settings'])->name('settings');

    Route::group(['prefix' => 'register', 'as' => 'register.'], function () {
        Route::post('', [AuthController::class, 'register'])->name('store')->middleware('throttle:register');
        Route::post('check', [AuthController::class, 'checkUser'])->name('check');
    });

    Route::group(['prefix' => 'sms', 'as' => 'sms.'], function () {
        Route::post('send', [SmsController::class, 'smsSend'])->name('send')->middleware('throttle:sms');
        Route::post('code', [SmsController::class, 'smsCode'])->name('code')->middleware('throttle:otp');
    });

    Route::group(['prefix' => 'password', 'as' => 'password.'], function () {
        Route::get('find/{token}', [PasswordResetController::class, 'find'])->name('find');
        Route::post('create', [PasswordResetController::class, 'create'])->name('create')->middleware('throttle:otp');
        Route::post('otp', [PasswordResetController::class, 'otp'])->name('otp')->middleware('throttle:otp');
        Route::post('reset', [PasswordResetController::class, 'reset'])->name('reset')->middleware('throttle:otp');
    });

    Route::middleware(['check.lastactive'])->group(function () {

        Route::apiResource('cities', CityController::class)->only(['index', 'show']);

        Route::middleware('auth:sanctum')->group(function () {

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

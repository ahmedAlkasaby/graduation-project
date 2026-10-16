<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AjaxStatusController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\LoginAdminController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Every route here is named after its module and action ("users.edit"), which
| is what the `auto-check-permission` middleware matches permissions against.
| Keep that naming and a new module needs no per-route permission wiring.
|
*/

$admin_url = 'admin';

Route::group([], function () use ($admin_url) {
    Route::get($admin_url.'/login', [LoginAdminController::class, 'showLoginForm'])->name('admin.login');
    Route::post($admin_url.'/login', [LoginAdminController::class, 'login'])->name('admin.login.save')->middleware('throttle:5,1');
    Route::post($admin_url.'/logout', [LoginAdminController::class, 'logout'])->name('admin.logout');
});

Route::group([
    'prefix' => $admin_url,
    'as' => 'admin.',
    'middleware' => ['admin', 'site.open', 'locale.admin', 'check.lastactive.admin', 'auto-check-permission'],
], function () {

    // Landing + error pages
    Route::get('', [DashboardController::class, 'index'])->name('index');
    Route::get('404', [AdminController::class, 'pageError'])->name('404');
    Route::get('unauthorized', [AdminController::class, 'pageUnauthorized'])->name('unauthorized');

    // Settings
    Route::group(['prefix' => 'settings', 'as' => 'settings.', 'middleware' => ['access.all']], function () {
        Route::get('', [SettingController::class, 'setting'])->name('index');
        Route::post('', [SettingController::class, 'settingStore'])->name('store');
        Route::get('social', [SettingController::class, 'social'])->name('social');
        Route::post('social', [SettingController::class, 'socialStore'])->name('social.store');
    });

    // Roles
    Route::resource('roles', RoleController::class);

    // Users
    Route::get('users/trash', [UserController::class, 'trash'])->name('users.trash');
    Route::delete('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::get('users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::patch('users/{id}/update', [UserController::class, 'update'])->name('users.update');
    Route::post('users/status', [AjaxStatusController::class, 'userStatus'])->name('users.status');
    Route::resource('users', UserController::class)->except(['edit', 'update']);

    // Cities
    Route::post('cities/status', [AjaxStatusController::class, 'cityStatus'])->name('cities.status');
    Route::resource('cities', CityController::class);


    Route::resource('activity_logs', ActivityLogController::class)->only(['index']);
    Route::resource('logs', LogController::class)->only(['index']);

    // Per-user preferences
    Route::post('theme/change', [SettingController::class, 'changeTheme'])->name('theme.change');
    Route::post('lang/change', [SettingController::class, 'changeLang'])->name('lang.change');
});

/*
| Developer tools — owner only, and deliberately outside the permission
| middleware so they stay reachable while permissions are being changed.
*/
Route::group([
    'prefix' => $admin_url,
    'as' => 'admin.',
    'middleware' => ['admin', 'locale.admin', 'access.all'],
], function () {
    Route::get('optimize', [AdminController::class, 'getOptimize'])->name('optimize');
    Route::get('optimize/clear', [AdminController::class, 'getOptimizeClear'])->name('optimize.clear');
    Route::get('developer/setting', [AdminController::class, 'developerSetting'])->name('developer.setting');
    Route::post('developer/setting', [AdminController::class, 'developerSettingStore'])->name('developer.setting.store');
    Route::get('images/setting', [AdminController::class, 'imageSetting'])->name('images.setting');
    Route::post('images/setting', [AdminController::class, 'imageSettingStore'])->name('images.setting.store');
});

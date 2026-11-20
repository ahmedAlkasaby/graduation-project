<?php

use App\Http\Controllers\Admin\CaseAssistanceController;
use App\Http\Controllers\Admin\CharityCaseController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LoginAdminController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PreferenceController;
use App\Http\Controllers\Admin\RegionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin panel (Blade)
|--------------------------------------------------------------------------
|
| Served under /admin with the "web" middleware, named "admin.*".
|
| The server-rendered twin of routes/api/v1/admin.php: the same modules, the
| same actions and the same permission names, reusing the same FormRequests,
| builders and services. Only the response differs: views and redirects
| instead of JSON. A route added there gets its twin here.
|
| Every route inside the "auto-check-permission" group is permission checked
| by its name: "admin.cities.index" needs "cities.index". The create and edit
| screens are covered by the store and update permissions.
|
*/

Route::prefix('admin')->as('admin.')->group(function () {

    Route::middleware('guest')->group(function () {
        Route::get('login', [LoginAdminController::class, 'showLoginForm'])->name('login');
        Route::post('login', [LoginAdminController::class, 'login'])->name('login.save')->middleware('throttle:login');
    });

    Route::post('logout', [LoginAdminController::class, 'logout'])->name('logout');

    Route::middleware(['admin.panel', 'locale', 'check.lastactive'])->group(function () {

        // Open to every signed in admin: the landing page and their own preferences.
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::get('unauthorized', [DashboardController::class, 'unauthorized'])->name('unauthorized');
        Route::post('theme', [PreferenceController::class, 'theme'])->name('theme.change');
        Route::post('language', [PreferenceController::class, 'language'])->name('lang.change');

        Route::middleware('auto-check-permission')->group(function () {

            Route::patch('cities/{city}/status', [CityController::class, 'status'])->name('cities.status');
            Route::resource('cities', CityController::class);

            Route::patch('regions/{region}/status', [RegionController::class, 'status'])->name('regions.status');
            Route::resource('regions', RegionController::class);

            Route::patch('users/{user}/status', [UserController::class, 'status'])->name('users.status');
            Route::patch('users/{user}/restore', [UserController::class, 'restore'])->name('users.restore')->withTrashed();
            Route::resource('users', UserController::class);

            Route::resource('roles', RoleController::class);

            // Case management: beneficiary profiles and the assistances paid out to them.
            Route::get('charity-cases/lookup', [CharityCaseController::class, 'lookup'])
                ->name('charity_cases.lookup')
                ->permission('case_assistances.index', 'case_assistances.store');
            Route::resource('charity-cases', CharityCaseController::class)
                ->except('destroy')
                ->names('charity_cases')
                ->parameters(['charity-cases' => 'charityCase']);

            Route::patch('case-assistances/{caseAssistance}/status', [CaseAssistanceController::class, 'status'])->name('case_assistances.status');
            Route::patch('case-assistances/{caseAssistance}/installments/{installment}/cancel', [CaseAssistanceController::class, 'cancelInstallment'])
                ->name('case_assistances.cancel_installment')
                ->scopeBindings();
            Route::resource('case-assistances', CaseAssistanceController::class)
                ->except('destroy')
                ->names('case_assistances')
                ->parameters(['case-assistances' => 'caseAssistance']);

            Route::get('permissions', [PermissionController::class, 'index'])
                ->name('permissions.index')
                ->permission('roles.index');
        });
    });
});

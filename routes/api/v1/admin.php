<?php

use App\Http\Controllers\Api\V1\Admin\CityController;
use App\Http\Controllers\Api\V1\Admin\PermissionController;
use App\Http\Controllers\Api\V1\Admin\RegionController;
use App\Http\Controllers\Api\V1\Admin\RoleController;
use App\Http\Controllers\Api\V1\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API v1 — Admin
|--------------------------------------------------------------------------
|
| Served under /api/v1/admin, named "api.v1.admin.*".
|
| Dashboard management only. Every route here is permission checked by its
| name: "api.v1.admin.cities.index" requires the "cities.index" permission.
| Name each route after its module and action, matching config/laratrust_seeder.php, or
| set the permission yourself: ->permission('cities.export').
|
| Signing in to the dashboard (POST /api/v1/admin/login) is the one admin
| route open to guests, so it is registered in routes/api.php, outside this
| file's auth group. Other self-service endpoints an admin needs as a user
| (refresh, sign out, own profile, own notifications) live in public.php.
|
*/

Route::patch('cities/{city}/status', [CityController::class, 'status'])->name('cities.status');
Route::apiResource('cities', CityController::class);

Route::patch('regions/{region}/status', [RegionController::class, 'status'])->name('regions.status');
Route::apiResource('regions', RegionController::class);

Route::get('users/form-options', [UserController::class, 'formOptions'])
    ->name('users.form-options')
    ->permission('users.store', 'users.update');
Route::patch('users/{user}/status', [UserController::class, 'status'])->name('users.status');
Route::patch('users/{user}/restore', [UserController::class, 'restore'])->name('users.restore')->withTrashed();
Route::apiResource('users', UserController::class);

Route::apiResource('roles', RoleController::class);

Route::get('permissions', [PermissionController::class, 'index'])
    ->name('permissions.index')
    ->permission('roles.index');

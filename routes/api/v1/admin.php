<?php

use App\Http\Controllers\Api\V1\Admin\CityController;
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
| Name each route after its module and action, matching PermissionSeeder, or
| set the permission yourself: ->permission('cities.export').
|
| Self-service endpoints an admin needs as a user (sign in, own profile, own
| notifications) are not repeated here: they live in public.php.
|
*/

Route::patch('cities/{city}/status', [CityController::class, 'status'])->name('cities.status');
Route::apiResource('cities', CityController::class);

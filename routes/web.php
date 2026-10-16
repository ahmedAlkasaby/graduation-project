<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| The project is API only: the dashboard and the mobile app both talk to
| routes/api.php. Nothing is rendered here.
|
*/

Route::get('/', fn () => response()->json([
    'name' => config('app.name'),
    'status' => 'ok',
]))->name('home');

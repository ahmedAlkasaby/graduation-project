<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| The starter ships no public site — a project adds its own front controllers
| and views here. Signing in happens in the admin panel only: see the
| admin.login routes in routes/admin.php.
|
*/

Route::get('/', fn () => redirect()->route('admin.index'))->name('home');

// Shown when the site_open setting is off.
Route::view('close', 'errors.close')->name('close');

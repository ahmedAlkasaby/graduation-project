<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| Closure based console commands, and the application's schedule. Both live
| here rather than in an App\Console\Kernel: Laravel resolves its own console
| kernel, so a Kernel class in this app would be loaded by nothing and any
| schedule defined in it would never fire.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');



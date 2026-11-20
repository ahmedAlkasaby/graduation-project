<?php

use App\Providers\AppServiceProvider;
use App\Providers\AuthServiceProvider;
use App\Providers\EventServiceProvider;
use App\Providers\RouteServiceProvider;
use App\Providers\SmsServiceProvider;

/*
| The application's own providers. Framework providers come from
| ServiceProvider::defaultProviders() and package providers from composer
| discovery, so neither is listed here — that is what keeps a framework
| upgrade able to add new services without this file being edited.
*/

return [
    AppServiceProvider::class,
    AuthServiceProvider::class,
    EventServiceProvider::class,
    RouteServiceProvider::class,
    SmsServiceProvider::class,
];

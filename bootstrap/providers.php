<?php

/*
| The application's own providers. Framework providers come from
| ServiceProvider::defaultProviders() and package providers from composer
| discovery, so neither is listed here — that is what keeps a framework
| upgrade able to add new services without this file being edited.
*/

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    App\Providers\EventServiceProvider::class,
    App\Providers\RouteServiceProvider::class,
    App\Providers\SmsServiceProvider::class,
];

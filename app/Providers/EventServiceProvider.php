<?php

namespace App\Providers;

use App\Models\Setting;
use App\Models\User;
use App\Observers\SettingObserver;
use App\Observers\UserObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    public function boot(): void
    {
        User::observe(UserObserver::class);
        Setting::observe(SettingObserver::class);
    }
}

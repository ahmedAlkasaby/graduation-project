<?php

namespace App\Providers;

use App\Contracts\SmsServiceInterface;
use App\Services\Sms\SmsManager;
use Illuminate\Support\ServiceProvider;


class SmsServiceProvider extends ServiceProvider
{
    
    public function register(): void
    {
        $this->app->singleton(SmsManager::class, function ($app) {
            return new SmsManager();
        });

        $this->app->bind(SmsServiceInterface::class, function ($app) {
            return $app->make(SmsManager::class)->resolve();
        });
    }

   
    public function boot(): void
    {
        
    }
}

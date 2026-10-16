<?php

namespace App\Services\Sms;

use App\Contracts\SmsServiceInterface;
use App\Models\Setting;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class SmsManager
{
    protected array $providers = [
        'log' => LogSmsService::class,
        'kwtsms' => KwtSmsService::class,
        'twilio' => TwilioSmsService::class,
        'vonage' => VonageSmsService::class,
    ];

    public function resolve(): SmsServiceInterface
    {
        $providerKey = $this->getActiveProviderKey();

        if (! isset($this->providers[$providerKey])) {
            // An unknown provider is a misconfiguration, but it must not take
            // the app down — fall back to the log driver and say so.
            Log::warning("SMS provider [{$providerKey}] is not supported; falling back to the log driver.", [
                'supported' => array_keys($this->providers),
            ]);

            $providerKey = 'log';
        }

        return App::make($this->providers[$providerKey]);
    }

    /**
     * The setting wins over config, so the provider can be switched without a
     * deploy. Reading it must not fail before the settings table exists.
     */
    protected function getActiveProviderKey(): string
    {
        $default = config('sms.default_provider', 'log');

        return (string) (settings()->get('sms_provider') ?: $default);
    }

    public function getAvailableProviders(): array
    {
        return array_keys($this->providers);
    }
}

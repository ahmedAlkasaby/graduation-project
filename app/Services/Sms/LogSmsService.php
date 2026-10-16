<?php

namespace App\Services\Sms;

use App\Contracts\SmsServiceInterface;
use App\DTOs\SmsResponseDTO;
use Illuminate\Support\Facades\Log;

/**
 * Writes the message to the log instead of sending it. This is the default
 * provider so a fresh clone can run the whole signup-with-code flow before
 * anyone has an SMS account, and so tests never hit a network.
 *
 * Switch to a real provider with SMS_PROVIDER in .env, or with the
 * `sms_provider` setting.
 */
class LogSmsService implements SmsServiceInterface
{
    public function send(string $mobile, string $message): SmsResponseDTO
    {
        Log::channel(config('sms.log_channel', 'stack'))->info('SMS (not sent — log driver)', [
            'mobile' => $mobile,
            'message' => $message,
        ]);

        return SmsResponseDTO::success(
            message: 'SMS written to the log',
            meta: ['mobile' => $mobile],
        );
    }

    public function getProviderKey(): string
    {
        return 'log';
    }
}

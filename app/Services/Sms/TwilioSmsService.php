<?php

namespace App\Services\Sms;

use App\Contracts\SmsServiceInterface;
use App\DTOs\SmsResponseDTO;
use App\Exceptions\SmsAuthenticationException;
use App\Exceptions\SmsInvalidNumberException;
use App\Exceptions\SmsNetworkException;
use App\Exceptions\SmsProviderException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Twilio SMS provider implementation.
 *
 * WHY: Each provider contains only its own API integration logic.
 * Twilio uses Basic Auth + a different request/response format.
 */
class TwilioSmsService implements SmsServiceInterface
{
    protected string $accountSid;
    protected string $authToken;
    protected string $fromNumber;

    public function __construct()
    {
        $this->accountSid = (string) config('twilio.account_sid');
        $this->authToken  = (string) config('twilio.auth_token');
        $this->fromNumber = (string) config('twilio.from_number');
    }

    public function send(string $mobile, string $message): SmsResponseDTO
    {
        // Twilio requires E.164 format (+<country_code><number>)
        if (!str_starts_with($mobile, '+')) {
            $mobile = '+' . $mobile;
        }

        $url = "https://api.twilio.com/2010-04-01/Accounts/{$this->accountSid}/Messages.json";

        try {
            $response = Http::asForm()
                ->withBasicAuth($this->accountSid, $this->authToken)
                ->timeout(15)
                ->retry(2, 500)
                ->post($url, [
                    'To'   => $mobile,
                    'From' => $this->fromNumber,
                    'Body' => $message,
                ]);

            if ($response->successful()) {
                Log::channel('sms')->info('Twilio SMS sent', [
                    'mobile' => $mobile,
                    'sid'    => $response->json('sid'),
                ]);

                return SmsResponseDTO::success(
                    message: 'SMS sent via Twilio',
                    messageId: $response->json('sid'),
                    meta: $response->json() ?? [],
                );
            }

            $errorCode = $response->json('code');

            // Authentication
            if ($response->status() === 401) {
                Log::channel('sms')->error('Twilio authentication failed', [
                    'status' => $response->status(),
                ]);

                throw new SmsAuthenticationException(
                    'Twilio authentication failed',
                    $this->getProviderKey(),
                    $response->status()
                );
            }

            // Invalid number (Twilio error code 21211)
            if ($errorCode === 21211 || $errorCode === 21614) {
                Log::channel('sms')->warning('Twilio invalid number', [
                    'mobile' => $mobile,
                    'code'   => $errorCode,
                ]);

                throw new SmsInvalidNumberException(
                    'Invalid mobile number for Twilio',
                    $this->getProviderKey(),
                    $response->status(),
                    ['mobile' => $mobile, 'twilio_code' => $errorCode]
                );
            }

            Log::channel('sms')->error('Twilio provider error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            throw new SmsProviderException(
                'Twilio returned an error: ' . $response->json('message', $response->body()),
                $this->getProviderKey(),
                $response->status(),
                ['response' => $response->json()]
            );

        } catch (ConnectionException $e) {
            Log::channel('sms')->critical('Twilio network failure', [
                'mobile' => $mobile,
                'error'  => $e->getMessage(),
            ]);

            throw new SmsNetworkException(
                'Could not connect to Twilio service',
                $this->getProviderKey(),
                0,
                [],
                $e
            );
        }
    }

    public function getProviderKey(): string
    {
        return 'twilio';
    }
}

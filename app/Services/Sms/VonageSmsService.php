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
 * Vonage (formerly Nexmo) SMS provider implementation.
 *
 * WHY: Demonstrates a third provider following the same interface contract.
 * Adding Vonage required ZERO changes to controllers or business logic.
 */
class VonageSmsService implements SmsServiceInterface
{
    protected string $apiKey;
    protected string $apiSecret;
    protected string $fromName;

    public function __construct()
    {
        $this->apiKey    = (string) config('vonage.api_key');
        $this->apiSecret = (string) config('vonage.api_secret');
        $this->fromName  = (string) config('vonage.from_name');
    }

    public function send(string $mobile, string $message): SmsResponseDTO
    {
        try {
            $response = Http::acceptJson()
                ->timeout(15)
                ->retry(2, 500)
                ->post('https://rest.nexmo.com/sms/json', [
                    'api_key'    => $this->apiKey,
                    'api_secret' => $this->apiSecret,
                    'from'       => $this->fromName,
                    'to'         => $mobile,
                    'text'       => $message,
                ]);

            if ($response->successful()) {
                $messages = $response->json('messages.0', []);
                $status   = $messages['status'] ?? '99';

                // Vonage uses status "0" for success
                if ($status === '0') {
                    Log::channel('sms')->info('Vonage SMS sent', [
                        'mobile'     => $mobile,
                        'message_id' => $messages['message-id'] ?? null,
                    ]);

                    return SmsResponseDTO::success(
                        message: 'SMS sent via Vonage',
                        messageId: $messages['message-id'] ?? null,
                        meta: $messages,
                    );
                }

                // Status 4 = Invalid credentials
                if ($status === '4') {
                    Log::channel('sms')->error('Vonage authentication failed', [
                        'error_text' => $messages['error-text'] ?? '',
                    ]);

                    throw new SmsAuthenticationException(
                        'Vonage authentication failed: ' . ($messages['error-text'] ?? ''),
                        $this->getProviderKey(),
                        401
                    );
                }

                // Status 6 = Invalid number
                if ($status === '6') {
                    Log::channel('sms')->warning('Vonage invalid number', [
                        'mobile' => $mobile,
                    ]);

                    throw new SmsInvalidNumberException(
                        'Invalid number for Vonage: ' . ($messages['error-text'] ?? ''),
                        $this->getProviderKey(),
                        422,
                        ['mobile' => $mobile]
                    );
                }

                // Any other Vonage error
                Log::channel('sms')->error('Vonage provider error', [
                    'status'     => $status,
                    'error_text' => $messages['error-text'] ?? '',
                ]);

                throw new SmsProviderException(
                    'Vonage error: ' . ($messages['error-text'] ?? 'Unknown error'),
                    $this->getProviderKey(),
                    (int) $status,
                    ['response' => $messages]
                );
            }

            Log::channel('sms')->error('Vonage HTTP error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            throw new SmsProviderException(
                'Vonage HTTP error: ' . $response->status(),
                $this->getProviderKey(),
                $response->status()
            );

        } catch (ConnectionException $e) {
            Log::channel('sms')->critical('Vonage network failure', [
                'mobile' => $mobile,
                'error'  => $e->getMessage(),
            ]);

            throw new SmsNetworkException(
                'Could not connect to Vonage service',
                $this->getProviderKey(),
                0,
                [],
                $e
            );
        }
    }

    public function getProviderKey(): string
    {
        return 'vonage';
    }
}

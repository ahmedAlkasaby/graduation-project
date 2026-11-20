<?php

namespace App\Services\Api\Auth\Social;

use App\Contracts\SocialTokenVerifierInterface;
use App\DTOs\SocialUserDTO;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;


class AppleTokenVerifier implements SocialTokenVerifierInterface
{
    private const KEYS_URL = 'https://appleid.apple.com/auth/keys';

    private const KEYS_CACHE_KEY = 'apple:auth-keys';

    private const ISSUER = 'https://appleid.apple.com';

    public function verify(string $token): ?SocialUserDTO
    {
        $audience = config('services.apple.client_id');

        if (blank($audience)) {
            Log::warning('Apple sign-in attempted with no APPLE_CLIENT_ID configured');

            return null;
        }

        try {
            $claims = JWT::decode($token, JWK::parseKeySet($this->keys()));
        } catch (Throwable $e) {
            Cache::forget(self::KEYS_CACHE_KEY);

            Log::warning('Apple token verify failed: '.$e->getMessage());

            return null;
        }

        if (($claims->aud ?? null) !== $audience || ($claims->iss ?? null) !== self::ISSUER) {
            return null;
        }

        $email = $claims->email ?? "{$claims->sub}@apple.com";

        return new SocialUserDTO(
            id: (string) $claims->sub,
            email: $email,
            name: $email,
        );
    }

    /** @return array<string, mixed> */
    private function keys(): array
    {
        return Cache::remember(
            self::KEYS_CACHE_KEY,
            now()->addDay(),
            fn () => Http::timeout(5)->retry(2, 200)->get(self::KEYS_URL)->throw()->json(),
        );
    }
}

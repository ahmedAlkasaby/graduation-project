<?php

namespace App\Services\Api\Auth\Social;

use App\Contracts\SocialTokenVerifierInterface;
use App\DTOs\SocialUserDTO;
use Illuminate\Support\Facades\Http;

/** Checks a Google ID token against Google's tokeninfo endpoint. */
class GoogleTokenVerifier implements SocialTokenVerifierInterface
{
    public function verify(string $token): ?SocialUserDTO
    {
        $response = Http::timeout(5)->get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $token,
        ]);

        if (! $response->ok() || blank($response['sub'] ?? null)) {
            return null;
        }

        return new SocialUserDTO(
            id: (string) $response['sub'],
            email: $response['email'] ?? null,
            name: $response['name'] ?? null,
        );
    }
}

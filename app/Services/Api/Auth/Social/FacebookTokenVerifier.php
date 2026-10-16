<?php

namespace App\Services\Api\Auth\Social;

use App\Contracts\SocialTokenVerifierInterface;
use App\DTOs\SocialUserDTO;
use Illuminate\Support\Facades\Http;


class FacebookTokenVerifier implements SocialTokenVerifierInterface
{
    public function verify(string $token): ?SocialUserDTO
    {
        $response = Http::timeout(5)->get('https://graph.facebook.com/me', [
            'fields' => 'id,name,email',
            'access_token' => $token,
        ]);

        if (! $response->ok() || blank($response['id'] ?? null)) {
            return null;
        }

        $id = (string) $response['id'];

        return new SocialUserDTO(
            id: $id,
            email: $response['email'] ?? "{$id}@facebook.com",
            name: $response['name'] ?? null,
        );
    }
}

<?php

namespace App\DTOs;

use App\Models\User;
use Carbon\CarbonInterface;

readonly class AuthenticatedUserDTO
{
    public function __construct(
        public User $user,
        public string $accessToken,
        public string $refreshToken,
        public CarbonInterface $accessTokenExpiresAt,
        public CarbonInterface $refreshTokenExpiresAt,
        public string $tokenType = 'Bearer',
    ) {}

    /**
     * @return array{access_token: string, refresh_token: string, token_type: string, expires_in: int, access_token_expires_at: string, refresh_token_expires_at: string}
     */
    public function tokenData(): array
    {
        return [
            'access_token' => $this->accessToken,
            'refresh_token' => $this->refreshToken,
            'token_type' => $this->tokenType,
            'expires_in' => (int) now()->diffInSeconds($this->accessTokenExpiresAt),
            'access_token_expires_at' => $this->accessTokenExpiresAt->toIso8601String(),
            'refresh_token_expires_at' => $this->refreshTokenExpiresAt->toIso8601String(),
        ];
    }
}

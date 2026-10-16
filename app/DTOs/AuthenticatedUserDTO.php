<?php

namespace App\DTOs;

use App\Models\User;


readonly class AuthenticatedUserDTO
{
    public function __construct(
        public User $user,
        public string $accessToken,
        public string $tokenType = 'Bearer',
    ) {}


    public function tokenData(): array
    {
        return [
            'access_token' => $this->accessToken,
            'token_type' => $this->tokenType,
        ];
    }
}

<?php

namespace App\Contracts;

use App\DTOs\SocialUserDTO;

interface SocialTokenVerifierInterface
{
    /** The identity behind the token, or null when the provider rejects it. */
    public function verify(string $token): ?SocialUserDTO;
}

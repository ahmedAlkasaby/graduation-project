<?php

namespace App\DTOs;

/** The identity a provider vouched for once its token checked out. */
readonly class SocialUserDTO
{
    public function __construct(
        public string $id,
        public ?string $email,
        public ?string $name,
    ) {}

    /** What to show as a name: the provider's name, else the email. */
    public function displayName(): ?string
    {
        return $this->name ?: $this->email;
    }
}

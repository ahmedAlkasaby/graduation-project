<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

/** Which credential customers sign in with, configured per installation. */
enum LoginMethodEnum: string
{
    use HasOptions;

    case Email = 'email';
    case Phone = 'phone';

    public function label(): string
    {
        return match ($this) {
            self::Email => __('Email'),
            self::Phone => __('Phone'),
        };
    }
}

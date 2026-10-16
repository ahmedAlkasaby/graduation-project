<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

/** The identity providers a customer can sign in with from the app. */
enum SocialProviderEnum: string
{
    use HasOptions;

    case Google = 'google';
    case Facebook = 'facebook';
    case Apple = 'apple';

    public function label(): string
    {
        return match ($this) {
            self::Google => __('Google'),
            self::Facebook => __('Facebook'),
            self::Apple => __('Apple'),
        };
    }
}

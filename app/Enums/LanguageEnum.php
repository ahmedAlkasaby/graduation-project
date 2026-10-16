<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

/**
 * The languages the storefront and admin panel are translated into. These are
 * also the keys inside every translatable JSON column (`name`, `content`, ...).
 */
enum LanguageEnum: string
{
    use HasOptions;

    case Arabic = 'ar';
    case English = 'en';

    public function label(): string
    {
        return match ($this) {
            self::Arabic => __('Arabic'),
            self::English => __('English'),
        };
    }

    public function isRightToLeft(): bool
    {
        return $this === self::Arabic;
    }

    public function direction(): string
    {
        return $this->isRightToLeft() ? 'rtl' : 'ltr';
    }
}

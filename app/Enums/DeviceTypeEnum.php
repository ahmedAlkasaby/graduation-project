<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

/** The platform a device signs in from; decides how push notifications reach it. */
enum DeviceTypeEnum: string
{
    use HasOptions;

    case Android = 'android';
    case Huawei = 'huawei';
    case Apple = 'apple';
    case Website = 'website';

    public function label(): string
    {
        return match ($this) {
            self::Android => __('Android'),
            self::Huawei => __('Huawei'),
            self::Apple => __('Apple'),
            self::Website => __('Website'),
        };
    }

    /**
     * The mobile platforms, for endpoints only the app calls.
     *
     * @return array<int, self>
     */
    public static function mobile(): array
    {
        return [self::Android, self::Huawei, self::Apple];
    }
}

<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;


enum UserTypeEnum: string
{
    use HasOptions;

    case Donor = 'donor';
    case Volunteer = 'volunteer';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Donor => __('Donor'),
            self::Volunteer => __('Volunteer'),
            self::Admin => __('Admin'),
        };
    }

    public function isAdmin(): bool
    {
        return $this === self::Admin;
    }

   
    public static function appTypes(): array
    {
        return [self::Donor, self::Volunteer];
    }

  
    public static function appValues(): array
    {
        return array_map(fn (self $type) => $type->value, self::appTypes());
    }

   
    public static function audienceValues(?string $type): array
    {
        return $type === self::Admin->value ? [self::Admin->value] : self::appValues();
    }
}

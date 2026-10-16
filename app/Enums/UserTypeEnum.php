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

    /**
     * The stored values of appTypes(), e.g. for an `in:` rule or a whereIn.
     *
     * @return array<int, string>
     */
    public static function appValues(): array
    {
        return array_map(fn (self $type) => $type->value, self::appTypes());
    }

    /**
     * The stored values a request's `type` scopes a lookup to: just admins
     * when it asks for admin, otherwise every app user.
     *
     * @return array<int, string>
     */
    public static function audienceValues(?string $type): array
    {
        return $type === self::Admin->value ? [self::Admin->value] : self::appValues();
    }
}

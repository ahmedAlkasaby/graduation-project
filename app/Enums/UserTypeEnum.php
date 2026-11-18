<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum UserTypeEnum: string
{
    use HasOptions;

    case Donor = 'donor';
    case Volunteer = 'volunteer';
    case Admin = 'admin';
    case SuperAdmin = 'super_admin';

    public function label(): string
    {
        return match ($this) {
            self::Donor => __('Donor'),
            self::Volunteer => __('Volunteer'),
            self::Admin => __('Admin'),
            self::SuperAdmin => __('Super Admin'),
        };
    }

    /** Position in the hierarchy: super admin > admin > volunteer > donor. */
    public function rank(): int
    {
        return match ($this) {
            self::Donor => 1,
            self::Volunteer => 2,
            self::Admin => 3,
            self::SuperAdmin => 4,
        };
    }

    /** Whether this type may manage a user of $type: anyone at the same rank or below. */
    public function canManage(self $type): bool
    {
        return $this->rank() >= $type->rank();
    }

    /** Admin or super admin: anyone who uses the dashboard. */
    public function isAdmin(): bool
    {
        return in_array($this, self::adminTypes(), true);
    }

    /** Skips every dashboard permission check. */
    public function isSuperAdmin(): bool
    {
        return $this === self::SuperAdmin;
    }

    public static function adminTypes(): array
    {
        return [self::Admin, self::SuperAdmin];
    }

    public static function adminValues(): array
    {
        return array_map(fn (self $type) => $type->value, self::adminTypes());
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
        return in_array($type, self::adminValues(), true) ? self::adminValues() : self::appValues();
    }
}

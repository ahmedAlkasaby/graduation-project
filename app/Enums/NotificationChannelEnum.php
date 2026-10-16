<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

/** Where a notification is delivered when an admin sends one. */
enum NotificationChannelEnum: string
{
    use HasOptions;

    case All = 'all';
    case Firebase = 'firebase';
    case Database = 'database';

    public function label(): string
    {
        return match ($this) {
            self::All => __('All'),
            self::Firebase => __('firebase'),
            self::Database => __('database'),
        };
    }

    public function sendsPush(): bool
    {
        return in_array($this, [self::All, self::Firebase], true);
    }

    public function storesInDatabase(): bool
    {
        return in_array($this, [self::All, self::Database], true);
    }
}

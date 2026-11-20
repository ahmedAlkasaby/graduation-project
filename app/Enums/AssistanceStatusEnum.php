<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

/**
 * An assistance's state, read from `cancelled_at` / `finished_at` rather than
 * stored. Nothing sets those columns yet: the rules for finishing and
 * cancelling an assistance are still to be decided.
 */
enum AssistanceStatusEnum: string
{
    use HasOptions;

    case Open = 'open';
    case Finished = 'finished';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Open => __('Open'),
            self::Finished => __('Finished'),
            self::Cancelled => __('Cancelled'),
        };
    }

    /** The Bootstrap colour of this state's badge. */
    public function color(): string
    {
        return match ($this) {
            self::Open => 'primary',
            self::Finished => 'success',
            self::Cancelled => 'secondary',
        };
    }
}

<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

/**
 * An installment's state, read from its timestamps rather than stored:
 * `paid_at` set = paid, `cancelled_at` set = cancelled, neither = pending,
 * and a pending installment whose due date has passed is overdue. A due date
 * passing never makes an installment paid.
 */
enum InstallmentStatusEnum: string
{
    use HasOptions;

    case Pending = 'pending';
    case Overdue = 'overdue';
    case Paid = 'paid';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => __('Pending'),
            self::Overdue => __('Overdue'),
            self::Paid => __('Paid'),
            self::Cancelled => __('Cancelled'),
        };
    }

    /** The Bootstrap colour of this state's badge. */
    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Overdue => 'danger',
            self::Paid => 'success',
            self::Cancelled => 'secondary',
        };
    }
}

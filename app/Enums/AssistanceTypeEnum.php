<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

/**
 * How an assistance is paid out: once, or every month. The type decides which
 * amount column is used and how many installments are generated, so it can
 * never change after the assistance is created.
 */
enum AssistanceTypeEnum: string
{
    use HasOptions;

    case OneTime = 'one_time';
    case Recurring = 'recurring';

    public function label(): string
    {
        return match ($this) {
            self::OneTime => __('One-time'),
            self::Recurring => __('Monthly'),
        };
    }

    /** One line telling an admin what choosing this type means. */
    public function description(): string
    {
        return match ($this) {
            self::OneTime => __('Paid once, as a single installment on the due date you choose.'),
            self::Recurring => __('Paid every month on the 1st, from the start month to the end month.'),
        };
    }

    /** The case_assistances column that holds this type's amount. */
    public function amountColumn(): string
    {
        return match ($this) {
            self::OneTime => 'requested_amount',
            self::Recurring => 'amount_per_period',
        };
    }
}

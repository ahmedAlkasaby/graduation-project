<?php

namespace App\Models;

use App\Enums\InstallmentStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

/**
 * One due amount of an assistance.
 *
 * `amount` and `due_at` are fixed once generated. `paid_at` and
 * `cancelled_at` are not mass assignable: cancelling goes through
 * CaseAssistanceService, and paying will be recorded by the payments module.
 * An installment is never both paid and cancelled, and a paid one is never
 * deleted.
 */
class AssistanceInstallment extends SiteModel
{
    use HasFactory;

    /** Columns that may never change after the installment is generated. */
    public const IMMUTABLE = ['case_assistance_id', 'due_at', 'amount'];

    protected $table = 'assistance_installments';

    protected $fillable = ['case_assistance_id', 'due_at', 'amount', 'notes'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'due_at' => 'date',
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        parent::booted();

        static::saving(function (AssistanceInstallment $installment): void {
            if ($installment->paid_at !== null && $installment->cancelled_at !== null) {
                throw new LogicException('An installment cannot be both paid and cancelled.');
            }
        });

        static::updating(function (AssistanceInstallment $installment): void {
            if ($installment->isDirty(self::IMMUTABLE)) {
                throw new LogicException('The amount and due date of an installment cannot be changed.');
            }
        });

        static::deleting(function (AssistanceInstallment $installment): void {
            if ($installment->isPaid()) {
                throw new LogicException('A paid installment cannot be deleted.');
            }
        });
    }

    public function assistance(): BelongsTo
    {
        return $this->belongsTo(CaseAssistance::class, 'case_assistance_id');
    }

    public function isPaid(): bool
    {
        return $this->paid_at !== null;
    }

    public function isCancelled(): bool
    {
        return $this->cancelled_at !== null;
    }

    /** Neither paid nor cancelled: the only state that can still be cancelled. */
    public function isPending(): bool
    {
        return ! $this->isPaid() && ! $this->isCancelled();
    }

    public function status(): InstallmentStatusEnum
    {
        return match (true) {
            $this->isPaid() => InstallmentStatusEnum::Paid,
            $this->isCancelled() => InstallmentStatusEnum::Cancelled,
            $this->due_at->isPast() && ! $this->due_at->isToday() => InstallmentStatusEnum::Overdue,
            default => InstallmentStatusEnum::Pending,
        };
    }
}

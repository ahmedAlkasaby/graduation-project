<?php

namespace App\Models;

use App\Builders\CaseAssistanceBuilder;
use App\Enums\AssistanceStatusEnum;
use App\Enums\AssistanceTypeEnum;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LogicException;

/**
 * A support plan for a beneficiary, paid out through its installments.
 *
 * Its financial setup (beneficiary, type, amounts) is fixed once created:
 * changing it would make the plan disagree with its installments. The
 * requests reject such changes, and this model refuses to save them as the
 * last line of defence.
 */
class CaseAssistance extends SiteModelSoftDelete
{
    use HasFactory;

    /** Columns that may never change after creation. */
    public const IMMUTABLE = ['case_id', 'assistance_type', 'requested_amount', 'amount_per_period', 'created_by'];

    protected $table = 'case_assistances';

    protected $fillable = [
        'case_id', 'assistance_type', 'title', 'description', 'requested_amount', 'amount_per_period',
        'order_id', 'active', 'created_by',
    ];

    protected $searchable = ['title'];

    /**
     * Merged with SiteModelDefault's $casts, which make `title` translated JSON.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'assistance_type' => AssistanceTypeEnum::class,
            'requested_amount' => 'decimal:2',
            'amount_per_period' => 'decimal:2',
            'finished_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        parent::booted();

        static::updating(function (CaseAssistance $assistance): void {
            if ($assistance->isDirty(self::IMMUTABLE)) {
                throw new LogicException('The financial setup of an assistance cannot be changed after it is created.');
            }
        });
    }

    public function charityCase(): BelongsTo
    {
        return $this->belongsTo(CharityCase::class, 'case_id');
    }

    public function canceller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by')->withTrashed();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function installments(): HasMany
    {
        return $this->hasMany(AssistanceInstallment::class);
    }

    public function isRecurring(): bool
    {
        return $this->assistance_type === AssistanceTypeEnum::Recurring;
    }

    /** The one amount this type uses: the full amount, or the amount per month. */
    public function amount(): ?string
    {
        return $this->isRecurring() ? $this->amount_per_period : $this->requested_amount;
    }

    /** The earliest unpaid, uncancelled due date, when loaded by withInstallmentSummary(). */
    public function nextDueAt(): ?CarbonImmutable
    {
        return filled($this->next_due_at) ? CarbonImmutable::parse($this->next_due_at) : null;
    }

    public function isNextDueOverdue(): bool
    {
        return (bool) $this->nextDueAt()?->lessThan(today());
    }

    public function status(): AssistanceStatusEnum
    {
        return match (true) {
            $this->cancelled_at !== null => AssistanceStatusEnum::Cancelled,
            $this->finished_at !== null => AssistanceStatusEnum::Finished,
            default => AssistanceStatusEnum::Open,
        };
    }

    public function newEloquentBuilder($query): CaseAssistanceBuilder
    {
        return new CaseAssistanceBuilder($query);
    }
}

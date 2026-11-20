<?php

namespace App\Services\CharityCase;

use App\Enums\AssistanceTypeEnum;
use App\Models\AssistanceInstallment;
use App\Models\CaseAssistance;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Creates assistances with their installments, and the few changes allowed
 * afterwards.
 *
 * - One-time: one installment for the requested amount on the chosen date.
 * - Recurring: one installment per month, on the 1st, from the start month to
 *   the end month inclusive, or 12 months when no end month is given.
 *
 * The schedule is generated once, in the same transaction as the assistance,
 * and never regenerated: amounts, type and installment dates are fixed. After
 * creation only the beneficiary, category, title, description, order and
 * status change, and a pending installment can be cancelled. Nothing here
 * records a payment.
 */
class CaseAssistanceService
{
    public const DEFAULT_INSTALLMENTS = 12;

    /** A safety ceiling on how many monthly installments one assistance generates. */
    public const MAX_INSTALLMENTS = 120;

    /** What may change after creation. */
    public const EDITABLE = ['case_id', 'category_id', 'title', 'description', 'order_id', 'active'];

    /**
     * @param  array<string, mixed>  $data  validated CaseAssistanceRequest data
     */
    public function create(array $data, User $creator): CaseAssistance
    {
        $type = AssistanceTypeEnum::from($data['assistance_type']);
        $amount = $data[$type->amountColumn()];

        return DB::transaction(function () use ($data, $type, $amount, $creator): CaseAssistance {
            $assistance = CaseAssistance::create([
                'case_id' => $data['case_id'],
                'category_id' => $data['category_id'],
                'assistance_type' => $type,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'order_id' => $data['order_id'] ?? 1,
                'active' => $data['active'],
                'requested_amount' => $type === AssistanceTypeEnum::OneTime ? $amount : null,
                'amount_per_period' => $type === AssistanceTypeEnum::Recurring ? $amount : null,
                'created_by' => $creator->id,
            ]);

            $assistance->installments()->createMany(array_map(
                fn (string $dueAt): array => ['due_at' => $dueAt, 'amount' => $amount],
                $this->dueDates($type, $data),
            ));

            return $assistance;
        });
    }

    /**
     * The installment due dates (Y-m-d) an assistance of this type gets.
     *
     * @param  array{due_at?: string|null, start_month?: string|null, end_month?: string|null}  $data
     * @return list<string>
     */
    public function dueDates(AssistanceTypeEnum $type, array $data): array
    {
        if ($type === AssistanceTypeEnum::OneTime) {
            return [CarbonImmutable::parse($data['due_at'])->toDateString()];
        }

        $month = $this->firstOfMonth($data['start_month']);
        $last = filled($data['end_month'] ?? null)
            ? $this->firstOfMonth($data['end_month'])
            : $month->addMonths(self::DEFAULT_INSTALLMENTS - 1);

        $dates = [];

        while ($month->lessThanOrEqualTo($last)) {
            $dates[] = $month->toDateString();
            $month = $month->addMonth();
        }

        return $dates;
    }

    /** How many monthly installments a start and end month ("Y-m") cover, inclusive. */
    public function monthsBetween(string $startMonth, string $endMonth): int
    {
        return (int) $this->firstOfMonth($startMonth)->diffInMonths($this->firstOfMonth($endMonth)) + 1;
    }

    /**
     * Only the descriptive fields and the visibility: the financial setup and the schedule are
     * fixed, whatever else the data holds.
     *
     * @param  array<string, mixed>  $data  validated CaseAssistanceUpdateRequest data
     */
    public function update(CaseAssistance $assistance, array $data): CaseAssistance
    {
        $assistance->update(Arr::only($data, self::EDITABLE));

        return $assistance;
    }

    /**
     * Cancels one installment that is neither paid nor already cancelled.
     * The row is locked first, so a payment recorded at the same moment can
     * never end up on a cancelled installment.
     */
    public function cancelInstallment(AssistanceInstallment $installment, ?string $notes, User $cancelledBy): AssistanceInstallment
    {
        return DB::transaction(function () use ($installment, $notes, $cancelledBy): AssistanceInstallment {
            $locked = AssistanceInstallment::query()->lockForUpdate()->findOrFail($installment->id);

            if (! $locked->isPending()) {
                throw ValidationException::withMessages([
                    'installment' => __('Only an installment that is neither paid nor cancelled can be cancelled.'),
                ]);
            }

            $locked->forceFill([
                'cancelled_at' => now(),
                'cancelled_by' => $cancelledBy->id,
                'notes' => $notes,
            ])->save();

            return $locked;
        });
    }

    private function firstOfMonth(string $month): CarbonImmutable
    {
        return CarbonImmutable::createFromFormat('!Y-m', $month)->startOfMonth();
    }
}

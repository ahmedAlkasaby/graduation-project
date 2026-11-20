<?php

namespace App\Http\Resources;

/**
 * An assistance. `title` is read in the request's language, and
 * `title_translations` carries both for an edit form. Amounts are strings
 * with two decimals, in `currency`.
 */
class CaseAssistanceResource extends MainResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'case_id' => $this->case_id,
            'assistance_type' => $this->assistance_type->value,
            'assistance_type_label' => $this->assistance_type->label(),
            'title' => $this->translated($this->title),
            'title_translations' => $this->title,
            'description' => $this->description,
            'requested_amount' => $this->requested_amount,
            'amount_per_period' => $this->amount_per_period,
            'currency' => settings()->get('currency', 'EGP'),
            'order_id' => $this->order_id,
            'active' => (int) $this->active,
            'status' => $this->status()->value,
            'status_label' => $this->status()->label(),
            'finished_at' => $this->finished_at?->timezone(config('app.timezone'))->toDateTimeString(),
            'cancelled_at' => $this->cancelled_at?->timezone(config('app.timezone'))->toDateTimeString(),
            'cancelled_by' => $this->cancelled_by,
            'installments_count' => $this->whenCounted('installments'),
            'paid_installments_count' => $this->whenHas('paid_installments_count'),
            'next_due_at' => $this->whenHas('next_due_at'),
            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->timezone(config('app.timezone'))->toDateTimeString(),

            // relations
            'charity_case' => new CharityCaseResource($this->whenLoaded('charityCase')),
            'installments' => AssistanceInstallmentResource::collection($this->whenLoaded('installments')),
        ];
    }
}

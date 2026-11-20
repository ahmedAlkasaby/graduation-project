<?php

namespace App\Http\Resources;

/** One installment. `status` is derived from `paid_at` / `cancelled_at` and the due date. */
class AssistanceInstallmentResource extends MainResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'case_assistance_id' => $this->case_assistance_id,
            'due_at' => $this->due_at->toDateString(),
            'amount' => $this->amount,
            'status' => $this->status()->value,
            'status_label' => $this->status()->label(),
            'paid_at' => $this->paid_at?->timezone(config('app.timezone'))->toDateTimeString(),
            'cancelled_at' => $this->cancelled_at?->timezone(config('app.timezone'))->toDateTimeString(),
            'notes' => $this->notes,
        ];
    }
}

<?php

namespace App\Http\Resources;

class CaseCategoryResource extends MainResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->translated($this->name),
            'order_id' => $this->order_id,
            'active' => (int) $this->active,
            'assistances_count' => $this->whenCounted('assistances'),
            'created_at' => $this->created_at?->timezone(config('app.timezone'))->toDateTimeString(),
        ];
    }
}

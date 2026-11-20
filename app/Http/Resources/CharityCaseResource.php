<?php

namespace App\Http\Resources;

class CharityCaseResource extends MainResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'national_id' => $this->national_id,
            'phone' => $this->phone,
            'category_id' => $this->category_id,
            'volunteer_id' => $this->volunteer_id,
            'assistances_count' => $this->whenCounted('assistances'),
            'created_at' => $this->created_at?->timezone(config('app.timezone'))->toDateTimeString(),

            // relations
            'category' => new CaseCategoryResource($this->whenLoaded('category')),
            'volunteer' => $this->whenLoaded('volunteer', fn () => $this->volunteer ? [
                'id' => $this->volunteer->id,
                'full_name' => $this->volunteer->full_name,
                'phone' => $this->volunteer->phone,
            ] : null),
            'assistances' => CaseAssistanceResource::collection($this->whenLoaded('assistances')),
        ];
    }
}

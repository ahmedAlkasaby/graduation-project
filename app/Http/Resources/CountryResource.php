<?php

namespace App\Http\Resources;

class CountryResource extends MainResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->translated($this->name),
            'code' => $this->code,
            'phone_code' => $this->phone_code,
            'image' => $this->image,
            'order_id' => $this->order_id,
            'active' => (int) $this->active,
            'created_at' => $this->created_at?->timezone(config('app.timezone'))->toDateTimeString(),
        ];
    }
}

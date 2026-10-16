<?php

namespace App\Http\Resources;

class CityResource extends MainResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'country_id' => $this->country_id,
            'name' => $this->translated($this->name),
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'order_id' => $this->order_id,
            'active' => (int) $this->active,
            'created_at' => $this->created_at?->timezone(config('app.timezone'))->toDateTimeString(),

            // relations
            'country' => new CountryResource($this->whenLoaded('country')),
        ];
    }
}

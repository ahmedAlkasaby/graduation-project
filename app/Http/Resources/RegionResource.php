<?php

namespace App\Http\Resources;

class RegionResource extends MainResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'city_id' => $this->city_id,
            'name' => $this->translated($this->name),
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'order_id' => $this->order_id,
            'active' => (int) $this->active,
            'created_at' => $this->created_at?->timezone(config('app.timezone'))->toDateTimeString(),

            // relations
            'city' => new CityResource($this->whenLoaded('city')),
        ];
    }
}

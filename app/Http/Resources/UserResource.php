<?php

namespace App\Http\Resources;

class UserResource extends MainResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name_first' => $this->name_first,
            'name_last' => $this->name_last,
            'name' => $this->name,
            'full_name' => $this->full_name,
            'username' => $this->username,
            'phone' => $this->phone,
            'email' => $this->email,
            'image' => $this->image,
            'gender' => $this->gender,
            'gender_name' => labelForValue($this->gender),
            'birth_date' => $this->birth_date?->toDateString(),
            'country_id' => $this->country_id,
            'city_id' => $this->city_id,
            'locale' => $this->locale,
            'theme' => $this->theme,
            'is_notify' => (int) $this->is_notify,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'active' => (int) $this->active,
            'last_active' => $this->last_active?->toDateTimeString(),
            'created_at' => $this->created_at?->timezone(config('app.timezone'))->toDateTimeString(),

            // relations
            'country' => new CountryResource($this->whenLoaded('country')),
            'city' => new CityResource($this->whenLoaded('city')),
        ];
    }
}

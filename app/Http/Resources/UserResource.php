<?php

namespace App\Http\Resources;

class UserResource extends MainResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'image' => $this->image,
            'gender' => $this->gender,
            'gender_name' => labelForValue($this->gender),
            'birth_date' => $this->birth_date?->toDateString(),
            'locale' => $this->locale,
            'theme' => $this->theme,
            'is_notify' => (int) $this->is_notify,
            'last_active' => $this->last_active?->toDateTimeString(),
            'created_at' => $this->created_at?->timezone(config('app.timezone'))->toDateTimeString(),
        ];
    }
}

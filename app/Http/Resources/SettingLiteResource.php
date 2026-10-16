<?php

namespace App\Http\Resources;

use App\Http\Resources\MainResource;

class SettingLiteResource extends MainResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'key'        => $this->key,
            'value'      => $this->value,
        ];
    }
}

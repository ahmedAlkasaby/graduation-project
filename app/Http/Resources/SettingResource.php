<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class SettingResource extends MainResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'group' => $this->group,
            'type' => $this->type,
            'key' => $this->key,
            'value' => $this->value,
            'locale' => $this->locale,
            'autoload' => $this->autoload,
            'created_at' => $this->created_at->timezone(config('app.timezone'))->toDateTimeString(),
            // 'updated_at'    => $this->updated_at,

            // relations :
            'parent' => new SettingResource($this->whenLoaded('parent')),
        ];
    }
}

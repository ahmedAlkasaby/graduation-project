<?php

namespace App\Http\Resources;

use App\Http\Resources\MainResource;

class SettingResource extends MainResource
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
            'id'         => $this->id,
            'parent_id'  => $this->parent_id,
            'group'      => $this->group,
            'type'       => $this->type,
            'key'        => $this->key,
            'value'      => $this->value,
            'locale'     => $this->locale,
            'autoload'   => $this->autoload,
            'created_at' => $this->created_at->timezone(config('app.timezone'))->toDateTimeString(),
            // 'updated_at'    => $this->updated_at,

            // relations :
            'parent'     => new SettingResource($this->whenLoaded('parent')),
        ];
    }
}

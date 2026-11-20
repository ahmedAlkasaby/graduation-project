<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class MetaResource extends MainResource
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
            'user_id' => $this->user_id,
            'metaable_type' => $this->metaable_type,
            'metaable_id' => $this->metaable_id,
            'type' => $this->type,
            'key' => $this->key,
            'type_name' => labelForValue($this->type),
            'key_name' => labelForValue($this->key),
            'value' => $this->value,
            'group' => $this->group,
            'parent_id' => $this->parent_id,
            'created_at' => $this->created_at->timezone(config('app.timezone'))->toDateTimeString(),
            // // 'updated_at'    => $this->updated_at,

            // relations :
            // 'user'                 => new UserResource($this->whenLoaded('user')),
            'parent' => new MetaResource($this->whenLoaded('parent')),

        ];
    }
}

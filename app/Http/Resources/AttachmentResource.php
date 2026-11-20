<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class AttachmentResource extends MainResource
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
            'attachmentable_type' => $this->attachmentable_type,
            'attachmentable_id' => $this->attachmentable_id,
            'type' => $this->type,
            'key' => $this->key,
            'value' => $this->value,
            'created_at' => $this->created_at->timezone(config('app.timezone'))->toDateTimeString(),
            // 'updated_at'    => $this->updated_at,

            // relations :
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}

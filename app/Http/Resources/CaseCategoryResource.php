<?php

namespace App\Http\Resources;

class CaseCategoryResource extends MainResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->translated($this->name),
        ];
    }
}

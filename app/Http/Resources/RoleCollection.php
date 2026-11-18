<?php

namespace App\Http\Resources;

class RoleCollection extends MainResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection,
        ];
    }
}

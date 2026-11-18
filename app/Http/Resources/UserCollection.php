<?php

namespace App\Http\Resources;

class UserCollection extends MainResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection,
        ];
    }
}

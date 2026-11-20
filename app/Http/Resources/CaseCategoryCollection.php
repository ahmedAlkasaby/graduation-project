<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class CaseCategoryCollection extends MainResourceCollection
{
    /**
     * @param  Request  $request
     * @return array{data: mixed}
     */
    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
        ];
    }
}

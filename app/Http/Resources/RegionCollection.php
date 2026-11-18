<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class RegionCollection extends MainResourceCollection
{
    /**
     * @param  Request  $request
     * @return array{data: Collection<int, mixed>}
     */
    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
        ];
    }
}

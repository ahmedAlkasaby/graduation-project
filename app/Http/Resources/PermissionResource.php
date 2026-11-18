<?php

namespace App\Http\Resources;

class PermissionResource extends MainResource
{
    /**
     * @return array{id: int, name: string, display_name: ?string, module: string}
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'display_name' => $this->display_name,
            'module' => $this->description,
        ];
    }
}

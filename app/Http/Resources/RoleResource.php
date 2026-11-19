<?php

namespace App\Http\Resources;

class RoleResource extends MainResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'display_name' => $this->display_name,
            'description' => $this->description,
            'can_login_web' => (bool) $this->can_login_web,
            'can_login_mobile' => (bool) $this->can_login_mobile,
            'users_count' => $this->whenCounted('users'),
            'permissions_count' => $this->whenCounted('permissions'),
            'permissions' => $this->whenLoaded('permissions', fn () => $this->permissions->map(fn ($permission) => [
                'id' => $permission->id,
                'name' => $permission->name,
                'display_name' => $permission->display_name,
                'module' => $permission->description,
            ])->values()),
            'created_at' => $this->created_at?->timezone(config('app.timezone'))->toDateTimeString(),
            'updated_at' => $this->updated_at?->timezone(config('app.timezone'))->toDateTimeString(),
        ];
    }
}

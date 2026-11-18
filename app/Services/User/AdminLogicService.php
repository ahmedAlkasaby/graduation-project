<?php

namespace App\Services\User;

use App\Enums\UserTypeEnum;
use App\Models\Permission;
use App\Models\User;

/**
 * Who may create or edit whom, and what each admin sees on the dashboard.
 *
 * Each type manages its own rank and everything below it (see
 * UserTypeEnum::rank()):
 *
 *  - super admin : everyone
 *  - admin       : admins, volunteers and donors
 *  - volunteer   : volunteers and donors
 *  - donor       : donors (never reaches the dashboard anyway)
 */
class AdminLogicService
{
    /**
     * Each dashboard sidebar item and the permission(s) that open it. A list
     * opens the item when the user holds any one of them.
     *
     * @var array<string, string|list<string>>
     */
    private const SIDEBAR = [
        // 'is_dashboard' => 'dashboard',
        // 'is_statistics' => 'statistics',
        // 'is_cities' => 'cities.index',
        'is_users' => 'users.index',
        'is_roles' => 'roles.index',
        // 'is_activity_logs' => 'activity_logs.index',
        // 'is_logs' => 'logs.index',
        // 'is_images' => 'images.index',
        // 'is_settings' => 'settings.index',
    ];

    public function canManageType(?User $actor, ?string $type): bool
    {
        $actorType = $actor?->typeEnum();
        $targetType = UserTypeEnum::tryFrom((string) $type);

        return $actorType !== null && $targetType !== null && $actorType->canManage($targetType);
    }

    /**
     * Every permission name the user holds through their roles and direct
     * permissions; all of them for a super admin.
     *
     * @return list<string>
     */
    public function effectivePermissionsFor(User $user): array
    {
        $names = $user->isSuperAdmin()
            ? Permission::query()->pluck('name')
            : $user->allPermissions(['name'])->pluck('name');

        return $names->unique()->sort()->values()->all();
    }

    /**
     * The dashboard sidebar as true / false flags for the given permission names.
     *
     * @param  list<string>  $permissions
     * @return array<string, bool>
     */
    public function sidebar(array $permissions): array
    {
        return array_map(
            fn (string|array $needs): bool => array_intersect((array) $needs, $permissions) !== [],
            self::SIDEBAR,
        );
    }
}

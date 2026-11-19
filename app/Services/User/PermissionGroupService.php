<?php

namespace App\Services\User;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Permissions the way the role form offers them, read straight from the
 * `permissions` table: a resource permission's `description` holds its
 * module ("cities") and gets a card per module; a custom screen
 * (`dashboard`, `statistics`) has a null `description` and is offered on its own.
 *
 * Nobody is offered a permission they do not hold themselves (the rule
 * RoleService enforces on save); a super admin is offered all of them.
 */
class PermissionGroupService
{
    /** The shared CRUD actions, in the order a module's card lists them. */
    private const RESOURCE_ACTIONS = ['view', 'show', 'create', 'edit', 'status', 'delete'];

    /**
     * @return array{resources: Collection<string, Collection<int, Permission>>, custom: Collection<int, Permission>}
     */
    public function grouped(User $actor): array
    {
        $permissions = Permission::query()
            ->select(['id', 'name', 'display_name', 'description'])
            ->when(! $actor->isSuperAdmin(), fn (Builder $query) => $query->whereKey(
                $actor->allPermissions(['id'])->pluck('id')->all(),
            ))
            ->orderBy('description')
            ->orderBy('id')
            ->get();

        [$customPermissions, $resourcePermissions] = $permissions->partition(
            fn (Permission $permission): bool => $permission->description === null,
        );

        return [
            'resources' => $resourcePermissions
                ->groupBy('description')
                ->map(fn (Collection $items): Collection => $items
                    ->sortBy(fn (Permission $permission): int => $this->actionOrder((string) $permission->display_name))
                    ->values()),
            'custom' => $customPermissions->values(),
        ];
    }

    /**
     * Every permission, module by module, marked with whether $role holds it.
     * Modules come in name order with the custom permissions last.
     *
     * @return Collection<int, array{key: string, name: string, granted: int, total: int, percent: int, permissions: list<array{label: string, granted: bool}>}>
     */
    public function coverage(Role $role): Collection
    {
        $grantedIds = $role->permissions->pluck('id')->flip();

        return Permission::query()
            ->select(['id', 'name', 'display_name', 'description'])
            ->orderBy('id')
            ->get()
            ->groupBy(fn (Permission $permission): string => (string) $permission->description)
            ->sortKeys()
            ->sortBy(fn (Collection $permissions, string $module): int => $module === '' ? 1 : 0)
            ->map(function (Collection $permissions, string $module) use ($grantedIds): array {
                $isCustom = $module === '';

                $items = $permissions
                    ->sortBy(fn (Permission $permission): int => $isCustom ? 0 : $this->actionOrder((string) $permission->display_name))
                    ->map(fn (Permission $permission): array => [
                        'label' => $isCustom ? $this->customLabel($permission) : __(Str::headline($permission->label())),
                        'granted' => $grantedIds->has($permission->id),
                    ])
                    ->values();

                $granted = $items->where('granted', true)->count();

                return [
                    'key' => $isCustom ? 'custom' : $module,
                    'name' => $isCustom ? __('Custom Permissions') : $this->moduleLabel($module),
                    'granted' => $granted,
                    'total' => $items->count(),
                    'percent' => (int) round($granted / max($items->count(), 1) * 100),
                    'permissions' => $items->all(),
                ];
            })
            ->values();
    }

    /** What a module is called on the form. */
    public function moduleLabel(string $module): string
    {
        return __(Str::headline($module));
    }

    /** What a custom permission is called on the form: its name, translated as a headline. */
    public function customLabel(Permission $permission): string
    {
        return __(Str::headline($permission->name));
    }

    /** CRUD actions come first in their usual order; any other action (restore, approve...) follows. */
    private function actionOrder(string $action): int
    {
        $position = array_search($action, self::RESOURCE_ACTIONS, true);

        return $position === false ? count(self::RESOURCE_ACTIONS) : $position;
    }
}

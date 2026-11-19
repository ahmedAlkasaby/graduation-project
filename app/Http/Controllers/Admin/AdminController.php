<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\MainController;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\View as ViewFactory;

/**
 * Base for every Blade admin screen. The panel's own middleware
 * (routes/admin.php) has already checked the session and the permission, so
 * a screen only builds its data and returns a view or a redirect.
 */
class AdminController extends MainController
{
    /** The signed in admin. */
    protected function admin(): User
    {
        return auth('web')->user();
    }

    /** Marks the sidebar entry: the singular snake name of the module ("city"). */
    protected function setClass(string $class): void
    {
        $this->class = $class;
        ViewFactory::share('class', $class);
    }

    /**
     * The list page size: a valid `limit` from the filter, else the setting.
     *
     * @param  array<string, mixed>  $filters  validated filter request data
     */
    protected function perPage(array $filters): int
    {
        $limit = (int) ($filters['limit'] ?? 0);

        return $limit > 0 ? $limit : $this->limit;
    }

    /**
     * What the signed in admin may do on a module's list page, the Blade
     * twin of ApiHomeController::listPagePermissions().
     *
     * @return array{route_create: bool, route_edit: bool}
     */
    protected function listPageFlags(string $table): array
    {
        return [
            'route_create' => $this->can("{$table}.store"),
            'route_edit' => $this->can("{$table}.update"),
        ];
    }

    /** A super admin may do everything. */
    protected function can(string $permission): bool
    {
        $admin = $this->admin();

        return $admin->isSuperAdmin() || $admin->hasPermission($permission);
    }

    /**
     * @param  LengthAwarePaginator<int, mixed>  $data
     * @param  array<string, mixed>  $with
     */
    protected function listView(string $view, LengthAwarePaginator $data, string $table, array $with = []): View
    {
        return view($view, ['data' => $data] + $this->listPageFlags($table) + $with);
    }
}

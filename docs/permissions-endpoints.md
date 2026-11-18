# Permissions Endpoints (Dashboard Only)

Two API endpoints copied from the HR System project and set up for a project where **permissions are used by the web dashboard only** (no mobile app).

| Method | URL | Route name | Guard | Purpose |
|---|---|---|---|---|
| GET | `/api/permissions` | `api.permissions.index` | `auth` + `check.permission:roles.index` | Every permission, grouped by module, to build the **role create/edit form** |
| GET | `/api/profile/permissions` | `api.profile.permissions` | `auth` only | Which **dashboard sidebar items** the signed-in user may open (`true` / `false` flags) |

What changed from the HR project:
- `profile/permissions` always returns the dashboard sidebar. The `type=web` query parameter and the mobile-only rows are gone.
- The HR-only per-request-type block (`requests.is_holiday_requests`, …) is gone. Add your own nested groups the same way if you need them (see `sidebar()` below).
- `getModuleTableModelName()` is replaced by `__(Str::headline($module))`.

Requires the seeding from `laratrust-seeder.md`: permissions named `{module}.{action}`, whose `description` holds the module name. Custom screens (`dashboard`, `statistics`, `reports.*`) have `description = null`.

---

## 1. Response examples

All responses use one envelope: `{ "status": "success" | "error", "message": "...", "data": ... }`.

### `GET /api/permissions`

```json
{
  "status": "success",
  "message": "Success",
  "data": {
    "resources": [
      {
        "module": "departments",
        "module_name": "Departments",
        "permissions": [
          { "id": 12, "name": "departments.index",   "display_name": "view",   "module": "departments" },
          { "id": 15, "name": "departments.show",    "display_name": "show",   "module": "departments" },
          { "id": 13, "name": "departments.store",   "display_name": "create", "module": "departments" },
          { "id": 16, "name": "departments.update",  "display_name": "edit",   "module": "departments" },
          { "id": 14, "name": "departments.status",  "display_name": "status", "module": "departments" },
          { "id": 11, "name": "departments.destroy", "display_name": "delete", "module": "departments" }
        ]
      }
    ],
    "custom": [
      { "id": 90, "name": "dashboard",       "label": "Dashboard" },
      { "id": 91, "name": "dashboard.today", "label": "Dashboard Today" },
      { "id": 99, "name": "statistics",      "label": "Statistics" }
    ]
  }
}
```

In each module, the CRUD actions come first (view, show, create, edit, status, delete). Workflow actions (approve, reject, cancel, …) follow them.

### `GET /api/profile/permissions`

```json
{
  "status": "success",
  "message": "Success",
  "data": {
    "is_dashboard": true,
    "is_employees": true,
    "is_departments": false,
    "is_reports": true,
    "is_activity_logs": false,
    "is_settings": false
  }
}
```

A flag is `true` when the user holds **any one** of the permissions mapped to it. Super admin and admin user types get every flag `true`.

### `403` from `check.permission`

```json
{ "status": "error", "message": "You don't have permission to do this" }
```

---

## 2. Routes — `routes/api.php`

```php
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\ProfileController;

Route::name('api.')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {

        // Signed-in user only: no permission needed to know your own sidebar.
        Route::get('profile/permissions', [ProfileController::class, 'permissions'])
            ->name('profile.permissions');

        // The role form needs the full permission list; guard it with roles.index.
        Route::get('permissions', [PermissionController::class, 'index'])
            ->name('permissions.index')
            ->middleware('check.permission:roles.index');
    });
});
```

Register the middleware alias in `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'check.permission' => \App\Http\Middleware\CheckPermission::class,
    ]);
})
```

---

## 3. Response trait — `app/Traits/ApiResponseTrait.php`

Skip this if the project already has its own response envelope.

```php
<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponseTrait
{
    protected function successResponse(mixed $data = null, string $message = 'Success', int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    protected function errorResponse(string $message = 'Error', int $statusCode = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $statusCode);
    }
}
```

---

## 4. Endpoint 1 — `GET /api/permissions`

### `app/Services/User/PermissionGroupService.php`

```php
<?php

namespace App\Services\User;

use App\Models\Permission;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Permissions the way a role form offers them: a permission whose description
 * names its module is grouped under that module, and one without a description
 * is offered on its own as custom.
 */
class PermissionGroupService
{
    /** The shared CRUD actions, in the order a module's card lists them. */
    private const RESOURCE_ACTIONS = ['view', 'show', 'create', 'edit', 'status', 'delete'];

    /**
     * @return array{resources: Collection<string, Collection<int, Permission>>, custom: Collection<int, Permission>}
     */
    public function grouped(): array
    {
        $permissions = Permission::query()
            ->select('id', 'display_name', 'name', 'description')
            ->orderBy('name')
            ->orderBy('id')
            ->get();

        [$resourcePermissions, $customPermissions] = $permissions->partition(fn (Permission $permission): bool => filled($permission->description));

        return [
            'resources' => $resourcePermissions
                ->groupBy('description')
                ->map(fn (Collection $items): Collection => $items
                    ->sortBy(fn (Permission $permission): int => $this->actionOrder((string) $permission->display_name))
                    ->values()),
            'custom' => $customPermissions->values(),
        ];
    }

    /** The module a permission belongs to: "dashboard" for both `dashboard` and `dashboard.today`. */
    public static function moduleOf(string $permissionName): string
    {
        return Str::before($permissionName, '.');
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

    /** CRUD actions come first in their usual order; workflow actions (approve, reject, cancel...) follow. */
    private function actionOrder(string $action): int
    {
        $position = array_search($action, self::RESOURCE_ACTIONS, true);

        return $position === false ? count(self::RESOURCE_ACTIONS) : $position;
    }
}
```

### `app/Http/Resources/PermissionResource.php`

```php
<?php

namespace App\Http\Resources;

use App\Services\User\PermissionGroupService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
{
    /** @return array{id: int, name: string, display_name: ?string, module: string} */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'display_name' => $this->display_name,
            'module' => PermissionGroupService::moduleOf($this->name),
        ];
    }
}
```

### `app/Http/Controllers/Api/PermissionController.php`

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use App\Services\User\PermissionGroupService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;

class PermissionController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private readonly PermissionGroupService $permissionGroups) {}

    /**
     * The role form's permissions: each module's actions, then the custom
     * screens (dashboard cards, report sections, statistics).
     */
    public function index(): JsonResponse
    {
        $grouped = $this->permissionGroups->grouped();

        return $this->successResponse([
            'resources' => $grouped['resources']
                ->map(fn ($permissions, string $module): array => [
                    'module' => $module,
                    'module_name' => $this->permissionGroups->moduleLabel($module),
                    'permissions' => PermissionResource::collection($permissions),
                ])
                ->values(),
            'custom' => $grouped['custom']->map(fn (Permission $permission): array => [
                'id' => $permission->id,
                'name' => $permission->name,
                'label' => $this->permissionGroups->customLabel($permission),
            ]),
        ]);
    }
}
```

---

## 5. Endpoint 2 — `GET /api/profile/permissions`

### `app/Services/User/AdminLogicService.php`

Only the parts this endpoint and the middleware need. **Edit `SIDEBAR` to match the new dashboard's menu.**

```php
<?php

namespace App\Services\User;

use App\Models\Permission;
use App\Models\User;

/**
 * Who sees what on the dashboard, kept in one place so a project changes its
 * rules here only.
 */
class AdminLogicService
{
    /** User types that hold every permission without needing a role. */
    private const ACCESS_ALL_TYPES = ['super_admin', 'admin'];

    /**
     * Each dashboard sidebar item and the permission(s) that open it. A list
     * opens the item when the user holds any one of them.
     *
     * @var array<string, string|list<string>>
     */
    private const SIDEBAR = [
        'is_dashboard' => 'dashboard',
        'is_users' => 'users.index',
        'is_roles' => 'roles.index',
        'is_employees' => 'employees.index',
        'is_departments' => 'departments.index',
        'is_reports' => ['statistics', 'reports.attendance', 'reports.employees'],
        'is_activity_logs' => 'activity_logs.index',
        'is_translations' => 'translations.index',
        'is_logs' => 'logs.index',
        'is_settings' => 'settings.index',
    ];

    public function hasAccessAll(User $user): bool
    {
        return in_array($user->type, self::ACCESS_ALL_TYPES, true);
    }

    public function hasPermission(User $user, string $permission): bool
    {
        return $this->hasAccessAll($user) || $user->hasPermission($permission);
    }

    /**
     * Every permission name the user holds through their roles and direct
     * permissions; all of them for an access-all user type.
     *
     * @return list<string>
     */
    public function effectivePermissionsFor(User $user): array
    {
        $names = $this->hasAccessAll($user)
            ? Permission::query()->pluck('name')
            : $user->allPermissions(['name'])->pluck('name');

        return $names->unique()->sort()->values()->all();
    }

    /**
     * The dashboard sidebar as true/false flags for the given permission names.
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

        // Nested group example (HR project did this for request types):
        // $flags['requests'] = [
        //     'is_holiday_requests' => in_array('holiday_requests.index', $permissions, true),
        // ];
    }
}
```

> `user->type` is the user-type column (`super_admin`, `admin`, …). If the new project has no such column, base `hasAccessAll()` on a role instead, e.g. `$user->hasRole('super_admin')`.

### `app/Http/Controllers/Api/ProfileController.php` (add the method)

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\User\AdminLogicService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use ApiResponseTrait;

    /** Which dashboard sidebar items the signed-in user's permissions open. */
    public function permissions(Request $request, AdminLogicService $adminLogic): JsonResponse
    {
        return $this->successResponse($adminLogic->sidebar(
            $adminLogic->effectivePermissionsFor($request->user()),
        ));
    }
}
```

---

## 6. Permission check middleware — `app/Http/Middleware/CheckPermission.php`

Used as `check.permission:roles.index` on `/api/permissions`. With no argument it checks the route's own name without the `api.` prefix, so `api.departments.index` needs `departments.index`. A route that no permission claims is open to any signed-in user.

```php
<?php

namespace App\Http\Middleware;

use App\Models\Permission;
use App\Models\User;
use App\Services\User\AdminLogicService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    private const ROUTE_PREFIX = 'api.';

    public function __construct(private readonly AdminLogicService $adminLogic) {}

    public function handle(Request $request, Closure $next, ?string $routeName = null): Response
    {
        $user = $request->user();

        if (! $user instanceof User || $this->adminLogic->hasAccessAll($user)) {
            return $next($request);
        }

        $permission = $routeName === null
            ? Permission::nameForRoute($this->currentRouteName($request))
            : Permission::nameForRoute($routeName) ?? $routeName;

        if ($permission === null || $user->hasPermission($permission)) {
            return $next($request);
        }

        return response()->json([
            'status' => 'error',
            'message' => __("You don't have permission to do this"),
        ], Response::HTTP_FORBIDDEN);
    }

    private function currentRouteName(Request $request): string
    {
        $routeName = (string) $request->route()?->getName();

        return str_starts_with($routeName, self::ROUTE_PREFIX)
            ? substr($routeName, strlen(self::ROUTE_PREFIX))
            : $routeName;
    }
}
```

---

## 7. Permission model — `app/Models/Permission.php`

The middleware needs `nameForRoute()`, and `LaratrustSeeder` calls `forgetRouteMap()`. The name map is cached forever and cleared whenever a permission is saved or deleted. `create` / `edit` routes use the `store` / `update` permission.

```php
<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Laratrust\Models\Permission as LaratrustPermission;

class Permission extends LaratrustPermission
{
    public const ROUTE_MAP_CACHE_KEY = 'permissions:route-map';

    private const FORM_ACTIONS = ['create' => 'store', 'edit' => 'update'];

    protected $fillable = [
        'name', 'display_name', 'description',
    ];

    /** @var array<string, string>|null */
    protected static ?array $routeMap = null;

    protected static function booted(): void
    {
        static::saved(fn () => static::forgetRouteMap());
        static::deleted(fn () => static::forgetRouteMap());
    }

    /** The permission name that guards a route name, or null when none does. */
    public static function nameForRoute(string $route): ?string
    {
        $map = static::routeMap();

        if (isset($map[$route])) {
            return $map[$route];
        }

        foreach (self::FORM_ACTIONS as $form => $action) {
            if (str_ends_with($route, ".{$form}")) {
                return $map[substr($route, 0, -strlen($form)).$action] ?? null;
            }
        }

        return null;
    }

    /** @return array<string, string> */
    public static function routeMap(): array
    {
        return static::$routeMap ??= Cache::rememberForever(
            static::ROUTE_MAP_CACHE_KEY,
            fn (): array => static::query()->pluck('name', 'name')->all(),
        );
    }

    public static function forgetRouteMap(): void
    {
        static::$routeMap = null;

        Cache::forget(static::ROUTE_MAP_CACHE_KEY);
    }
}
```

---

## 8. Checklist for the new project

1. Laratrust installed; `User` uses `Laratrust\Traits\HasRolesAndPermissions` and implements `Laratrust\Contracts\LaratrustUser`.
2. `config/laratrust.php` → `models.permission` points to `App\Models\Permission`.
3. Permissions and roles seeded (see `laratrust-seeder.md`), including `roles.index`.
4. Add the files above, register the `check.permission` alias, and add the routes.
5. Edit `AdminLogicService::SIDEBAR` and `ACCESS_ALL_TYPES` for the new dashboard.
6. Sanity check:
   ```bash
   php artisan route:list --path=permissions
   ```

# Laratrust Roles & Permissions Seeding

Copied from the HR System project. One config file lists every permission and what each role holds; the seeders read it.

## Files

| File | Purpose |
|---|---|
| `config/laratrust_seeder.php` | Every permission (`{module}.{action}`) and each role's permissions (`'*'` = all) |
| `database/seeders/LaratrustSeeder.php` | Resets every role in the config to exactly its listed permissions (run on purpose) |
| `database/seeders/RoleSeeder.php` | Creates roles; fills a role only when first created, so later panel changes survive re-seeding |

## Usage

```bash
# normal seeding (DatabaseSeeder): PermissionSeeder first, then RoleSeeder
php artisan db:seed

# reset every configured role to exactly the config list
php artisan db:seed --class=LaratrustSeeder
```

In `DatabaseSeeder`:

```php
$this->call([
    PermissionSeeder::class, // creates the permission rows
    RoleSeeder::class,       // creates roles and attaches permissions
]);
```

## Before using it in another project

- **Permissions must already exist.** `LaratrustSeeder` / `RoleSeeder` only *attach* permissions; they look them up by name (`whereIn('name', ...)`). Something must create the rows first — in this project that is `PermissionSeeder` (project-specific, not included). Any permission listed in the config but missing from the table is silently skipped.
- **`Permission::forgetRouteMap()`** is a custom method on `App\Models\Permission` that clears a cached permission-name map. If the new project doesn't have it, remove that line from `LaratrustSeeder::run()`.
- **Models:** `App\Models\Role` and `App\Models\Permission` must extend `Laratrust\Models\Role` / `Laratrust\Models\Permission`.
- **Config:** replace the modules, actions and roles with the new project's own. Keep `super_admin` as the first role if your policies treat role id 1 as untouchable.
- Naming rule: `'module' => ['index', 'store']` → `module.index`, `module.store`; an empty action `''` → the bare module name (e.g. `statistics`).

---

## `config/laratrust_seeder.php`

```php
<?php

/*
|--------------------------------------------------------------------------
| Default roles and their permissions
|--------------------------------------------------------------------------
|
| One place to read every permission and what each seeded role holds.
| Permissions are named "{module}.{action}" after the routes they guard; list
| a module's actions, with '' for a permission named after the module itself
| ("statistics"). "*" means every permission there is.
|
| RoleSeeder fills a role from here when it is first created, so re-seeding
| keeps whatever HR changed later; `php artisan db:seed --class=LaratrustSeeder`
| resets every role below to exactly this list.
|
*/

/** The request types an employee files, reads and cancels, and reviews as a direct manager. */
$requestActions = ['index', 'show', 'store', 'cancel', 'approve', 'reject'];

$employee = [
    'employees' => ['index', 'show'],
    'reject_reasons' => ['index', 'show'],
    'devices' => ['index', 'store', 'cancel'],
    'tasks' => ['index', 'show', 'store', 'update', 'status', 'destroy', 'comments.store'],
    'task_folders' => ['index', 'show', 'store', 'update', 'destroy', 'statuses.update'],
    'task_statuses' => ['index'],
    'requests' => ['index', 'balance'],
    'daily_attendances' => ['index', 'store'],
    'payrolls' => ['index', 'show'],
    'reminders' => ['index', 'store', 'status'],
    'holiday_requests' => $requestActions,
    'permission_requests' => $requestActions,
    'overtime_requests' => $requestActions,
    'remote_requests' => $requestActions,
    'schedule_change_requests' => $requestActions,
    'compensatory_day_swap_requests' => $requestActions,
    'increase_requests' => $requestActions,
];

return [

    /*
     * Every permission in the system, as "{module}.{action}", in two groups.
     * '' stands for the permission named after the module itself ("dashboard").
     */
    'permissions_structure' => [

        // Modules backed by a model: its CRUD actions, then the workflow actions
        // that act on it (cancel, approve, reject, assign, suspend...).
        'resources' => [
            'approval_steps' => ['index', 'show', 'store', 'update', 'status', 'destroy'],
            'attendance_corrections' => ['index', 'show', 'store', 'update', 'status', 'destroy'],
            'attendance_logs' => ['index', 'show', 'store', 'update', 'status', 'destroy'],
            'cities' => ['index', 'show', 'store', 'update', 'status', 'destroy'],
            'compensatory_day_swap_requests' => ['index', 'show', 'store', 'update', 'status', 'destroy', 'cancel', 'approve', 'reject'],
            'daily_attendances' => ['index', 'show', 'store', 'update', 'status', 'destroy'],
            'departments' => ['index', 'show', 'store', 'update', 'status', 'destroy'],
            'devices' => ['index', 'show', 'store', 'update', 'status', 'destroy', 'cancel', 'approve', 'reject', 'suspend', 'reactivate', 'revoke'],
            'employees' => ['index', 'show', 'store', 'update', 'status', 'destroy'],
            'holiday_requests' => ['index', 'show', 'store', 'update', 'status', 'destroy', 'cancel', 'approve', 'reject'],
            'holidays' => ['index', 'show', 'store', 'update', 'status', 'destroy'],
            'increase_requests' => ['index', 'show', 'store', 'update', 'status', 'destroy', 'cancel', 'approve', 'reject'],
            'job_titles' => ['index', 'show', 'store', 'update', 'status', 'destroy'],
            'overtime_requests' => ['index', 'show', 'store', 'update', 'status', 'destroy', 'cancel', 'approve', 'reject'],
            'payroll_adjustments' => ['index', 'show', 'store', 'update', 'status', 'destroy'],
            'payroll_items' => ['index', 'show', 'store', 'update', 'status', 'destroy'],
            'payrolls' => ['index', 'show', 'store', 'update', 'destroy', 'finalize'],
            'permission_requests' => ['index', 'show', 'store', 'update', 'status', 'destroy', 'cancel', 'approve', 'reject'],
            'projects' => ['index', 'show', 'store', 'update', 'status', 'destroy'],
            'reasons' => ['index', 'show', 'store', 'update', 'status', 'destroy'],
            'reject_reasons' => ['index', 'show', 'store', 'update', 'status', 'destroy'],
            'reminders' => ['index', 'show', 'store', 'update', 'status', 'destroy'],
            'remote_requests' => ['index', 'show', 'store', 'update', 'status', 'destroy', 'cancel', 'approve', 'reject', 'assign'],
            'roles' => ['index', 'show', 'store', 'update', 'status', 'destroy'],
            'schedule_change_requests' => ['index', 'show', 'store', 'update', 'status', 'destroy', 'cancel', 'approve', 'reject'],
            'setting_approvable_request_steps' => ['index', 'show', 'store', 'update', 'status', 'destroy'],
            'task_assignees' => ['index', 'show', 'store', 'update', 'status', 'destroy'],
            'task_attachments' => ['index', 'show', 'store', 'update', 'status', 'destroy'],
            'task_comments' => ['index', 'show', 'store', 'update', 'status', 'destroy'],
            'task_folders' => ['index', 'show', 'store', 'update', 'status', 'destroy', 'statuses.update'],
            'task_statuses' => ['index'],
            'task_time_entries' => ['index', 'show', 'store', 'update', 'status', 'destroy'],
            'tasks' => ['index', 'show', 'store', 'update', 'status', 'destroy', 'comments.store'],
            'teams' => ['index', 'show', 'store', 'update', 'status', 'destroy'],
            'user_invitations' => ['index', 'destroy'],
            'users' => ['index', 'show', 'store', 'update', 'status', 'destroy', 'trash', 'restore'],
            'work_schedules' => ['index', 'show'],
            'activity_logs' => ['index'],
            'translations' => ['index'],
            'logs' => ['index'],
            'requests' => ['index', 'balance'],
            'settings' => ['index'],

        ],

        // Screens, cards and pages that are not a model's CRUD.
        'custom' => [
            'dashboard' => ['', 'today', 'attendance_trend', 'requests', 'pending_requests', 'tasks', 'reminders', 'attendance_overview'],
            'reports' => ['attendance', 'leave', 'tasks', 'payroll', 'employees', 'employee_overview'],
            'statistics' => [''],
        ],

    ],

    'roles_structure' => [

        'super_admin' => [
            'display_name' => 'Super Admin',
            'description' => 'Full access',
            'permissions' => '*',
        ],

        'admin' => [
            'display_name' => 'Admin',
            'description' => 'Admin',
            'permissions' => '*',
        ],

        // Their own requests, tasks, devices, attendance, payslips and reminders,
        // plus the colleague directory and the lookups the app's forms use.
        'employee' => [
            'display_name' => 'Employee',
            'description' => 'What every employee can do in the app',
            'permissions' => $employee,
        ],

        // Everything an employee does, plus the Reports screen. Their lists and
        // reports only cover their direct reports (EmployeeBuilder::visibleToUser()).
        'manager' => [
            'display_name' => 'Manager',
            'description' => 'What a direct manager can do in the app for their team',
            'permissions' => [...$employee, 'statistics' => ['']],
        ],

        // Given automatically to every account whose user type is `hr`. It starts
        // as what a manager holds; grant more from the role form or list it here.
        'hr' => [
            'display_name' => 'HR',
            'description' => 'What HR staff can do; given to every account of the hr user type',
            'permissions' => [...$employee, 'statistics' => ['']],
        ],

    ],

];
```

## `database/seeders/LaratrustSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use InvalidArgumentException;

/**
 * Resets every role in config/laratrust_seeder.php to exactly the permissions
 * listed there, creating the role if it is missing. Run it on purpose
 * (`php artisan db:seed --class=LaratrustSeeder`): unlike RoleSeeder it undoes
 * whatever was changed on those roles from the panel. Nothing is truncated, and
 * roles not in the config are left alone.
 */
class LaratrustSeeder extends Seeder
{
    public function run(): void
    {
        foreach (array_keys(self::roles()) as $name) {
            $role = self::createRole($name);
            $role->syncPermissions(self::permissionsFor($name));

            $this->command?->info("Synced role {$name}: {$role->permissions()->count()} permission(s)");
        }

        Permission::forgetRouteMap();
    }

    /**
     * @return array<string, array{display_name: string, description: string, permissions: '*'|array<string, list<string>>}>
     */
    public static function roles(): array
    {
        return config('laratrust_seeder.roles_structure', []);
    }

    /** Whether the role holds every permission there is. */
    public static function holdsEverything(string $role): bool
    {
        return self::definition($role)['permissions'] === '*';
    }

    /**
     * The permission names the config gives the role: "{module}.{action}" per
     * listed action, or the module name alone for ''.
     *
     * @return list<string>
     */
    public static function permissionNames(string $role): array
    {
        if (self::holdsEverything($role)) {
            return Permission::query()->pluck('name')->all();
        }

        $names = [];

        foreach (self::definition($role)['permissions'] as $module => $actions) {
            foreach ($actions as $action) {
                $names[] = $action === '' ? $module : "{$module}.{$action}";
            }
        }

        return array_values(array_unique($names));
    }

    /** @return Collection<int, Permission> */
    public static function permissionsFor(string $role)
    {
        return self::holdsEverything($role)
            ? Permission::all()
            : Permission::query()->whereIn('name', self::permissionNames($role))->get();
    }

    public static function createRole(string $name): Role
    {
        $definition = self::definition($name);

        return Role::firstOrCreate(
            ['name' => $name],
            ['display_name' => $definition['display_name'], 'description' => $definition['description']],
        );
    }

    /** @return array{display_name: string, description: string, permissions: '*'|array<string, list<string>>} */
    private static function definition(string $role): array
    {
        return self::roles()[$role] ?? throw new InvalidArgumentException("Role [{$role}] is not in config/laratrust_seeder.php.");
    }
}
```

## `database/seeders/RoleSeeder.php`

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Creates the roles listed in config/laratrust_seeder.php. Role 1 is
 * super_admin and is treated as untouchable by RolePolicy — every project keeps
 * it and adds its own roles in the config.
 *
 * A role holding every permission ("*") is re-synced each run, so it picks up
 * new permissions. Any other role is only filled when it is first created, so
 * re-seeding keeps whatever HR switched on or off since.
 */
class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (array_keys(LaratrustSeeder::roles()) as $name) {
            $role = LaratrustSeeder::createRole($name);

            if (LaratrustSeeder::holdsEverything($name) || $role->wasRecentlyCreated) {
                $role->syncPermissions(LaratrustSeeder::permissionsFor($name));
            }
        }
    }
}
```

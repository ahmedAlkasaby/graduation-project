# Roles Module — Blade Admin Dashboard (Laratrust)

A portable copy of the **Roles & Permissions** screen from the HR System admin panel:
list roles, create/edit a role with a grouped permission picker (per-module cards + "Custom permissions" tab with search, per-group and global "Select all"), a read-only "show" page grouped by module, and login-platform flags (web / mobile) per role.

---

## 1. Requirements

| Piece | Used for |
|---|---|
| `santigarcor/laratrust` | `roles`, `permissions`, `permission_role`, `role_user` tables + `syncPermissions()` |
| `spatie/laravel-html` (`html()` helper) | select fields in the shared form partials |
| Vuexy / Bootstrap 5 admin theme | classes like `switch`, `bg-label-*`, `ti ti-*` (Tabler icons) |
| Your admin base controller | `setClass()`, `$this->table`, `checkPerm()`, `getLimitView()`, `$this->limit` |

### Permission naming convention (important)

- Permission `name` = `{module}.{action}` → named after the route it guards, e.g. `roles.index`, `roles.store`, `tasks.comments.store`.
- Permission `description` = **module table name** (e.g. `roles`) for model-backed (resource) permissions → this is what groups them into a card.
- Permission with **empty `description`** → shown in the **Custom permissions** tab (e.g. `dashboard`, `statistics`, `reports.payroll`).
- `display_name` = the action (`view`, `show`, `create`, `edit`, `status`, `delete`, `approve`, ...) → used for the checkbox label and ordering.

---

## 2. Files overview

```
app/
├── Builders/RoleBuilder.php
├── Http/Controllers/Admin/RoleController.php
├── Http/Requests/Admin/RoleRequest.php
├── Models/Role.php
├── Models/Permission.php
├── Policies/RolePolicy.php
└── Services/User/PermissionGroupService.php
database/migrations/xxxx_add_login_platforms_to_roles_table.php
resources/views/admin/roles/
├── index.blade.php
├── create.blade.php
├── edit.blade.php
├── show.blade.php
└── components/
    ├── head-search.blade.php
    ├── table.blade.php
    ├── form.blade.php
    └── permissions-table.blade.php
routes/admin.php   → Route::resource('roles', RoleController::class);
```

---

## 3. Route

```php
// routes/admin.php (inside the admin prefix/name group: ->name('admin.'))
use App\Http\Controllers\Admin\RoleController;

Route::resource('roles', RoleController::class);
```

Sidebar link:

```blade
@can('roles.index')
    <li class="menu-item">
        <a href="{{ route('admin.roles.index') }}" class="menu-link">
            <i class="menu-icon ti ti-shield"></i>
            <div>{{ __('Roles') }}</div>
        </a>
    </li>
@endcan
```

---

## 4. Migration — login platforms on roles

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->boolean('can_login_web')->default(true)->after('description');
            $table->boolean('can_login_mobile')->default(true)->after('can_login_web');
        });
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn(['can_login_web', 'can_login_mobile']);
        });
    }
};
```

> Skip this migration (and remove the platform fields from the form/table/controller) if the other project has no web/mobile split.

---

## 5. Models

### `app/Models/Role.php`

```php
<?php

namespace App\Models;

use App\Builders\RoleBuilder;
use Illuminate\Support\Facades\DB;
use Laratrust\Models\Role as LaratrustRole;

class Role extends LaratrustRole
{
    protected $fillable = [
        'name', 'display_name', 'description', 'can_login_web', 'can_login_mobile',
    ];

    protected function casts(): array
    {
        return [
            'can_login_web' => 'boolean',
            'can_login_mobile' => 'boolean',
        ];
    }

    protected $searchable = [
        'name', 'display_name',
    ];

    /** Role does not extend the base SiteModel, so it opts into the shared builder here. */
    public function newEloquentBuilder($query): RoleBuilder
    {
        return new RoleBuilder($query);
    }

    public function getSearchable(): array
    {
        return $this->searchable;
    }

    /** No fulltext index on roles, so search always uses LIKE. */
    public function getFullTextSearchable(): array
    {
        return [];
    }

    public function getOrderByAllowed(): array
    {
        return ['id', 'name', 'display_name', 'created_at'];
    }

    public static function getRoleCount(int $roleId = 1): int
    {
        return DB::table(config('laratrust.tables.role_user'))
            ->where(config('laratrust.foreign_keys.role'), $roleId)
            ->where('user_type', (new User)->getMorphClass())
            ->count();
    }

    /** Why the role cannot be deleted, or null when no user holds it. */
    public function deletionBlocker(): ?string
    {
        return self::getRoleCount($this->id) > 0 ? __('Role has users') : null;
    }

    public function label(): string
    {
        return $this->display_name ?: $this->name;
    }
}
```

### `app/Models/Permission.php`

```php
<?php

namespace App\Models;

use Laratrust\Models\Permission as LaratrustPermission;

class Permission extends LaratrustPermission
{
    protected $fillable = [
        'name', 'display_name', 'description',
    ];

    public function label(): string
    {
        return $this->display_name ?: $this->name;
    }
}
```

> In the HR project the Permission model also caches a route → permission map used by the `check.permission` middleware. That part is unrelated to the roles screen and can be omitted.

### `app/Builders/RoleBuilder.php`

```php
<?php

namespace App\Builders;

class RoleBuilder extends BaseBuilder
{
    /**
     * Roles have no `active` flag and no `order_id`, so the list filters on the
     * search box alone and stays newest-first.
     */
    public function filter($request = null): static
    {
        $request = $request ?? request();

        return $this->mainSearch($this->searchTerm($request))->latest();
    }
}
```

> `BaseBuilder::mainSearch()` / `searchTerm()` are the project's shared search helpers. If the other project has no BaseBuilder, replace with:
>
> ```php
> public function filter($request = null): static
> {
>     $term = ($request ?? request())->input('search');
>
>     return $this->when($term, fn ($query) => $query->where(fn ($query) => $query
>         ->where('name', 'like', "%{$term}%")
>         ->orWhere('display_name', 'like', "%{$term}%")))
>         ->latest();
> }
> ```

---

## 6. Service — `app/Services/User/PermissionGroupService.php`

Groups permissions for the form: resource permissions grouped by `description` (module), custom ones (no description) listed alone.

```php
<?php

namespace App\Services\User;

use App\Models\Permission;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Permissions the way a role form offers them: a permission whose description names
 * its module's table is grouped under that module, and one without a description is
 * offered on its own as custom. Every permission in the table is offered.
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

        [$resourcePermissions, $customPermissions] = $permissions->partition(
            fn (Permission $permission): bool => filled($permission->description)
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

    /** The module a permission belongs to: "dashboard" for both `dashboard` and `dashboard.today`. */
    public static function moduleOf(string $permissionName): string
    {
        return Str::before($permissionName, '.');
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

---

## 7. Form Request — `app/Http/Requests/Admin/RoleRequest.php`

```php
<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roleId = $this->route('role');

        return [
            'name' => [
                'required', 'string', 'max:255',
                Rule::enum(UserTypeEnum::class), // remove if role names are free text in the other project
                Rule::unique('roles', 'name')->ignore($roleId),
            ],
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'can_login_web' => ['nullable', 'boolean'],
            'can_login_mobile' => ['nullable', 'boolean'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ];
    }
}
```

---

## 8. Policy — `app/Policies/RolePolicy.php`

Role `id = 1` is the owner role: only a holder of it can edit it, and nobody can delete it (prevents locking the panel).

```php
<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('roles.index');
    }

    public function view(User $user, Role $role): bool
    {
        return $user->can('roles.show');
    }

    public function create(User $user): bool
    {
        return $user->can('roles.store');
    }

    public function update(User $user, Role $role): bool
    {
        if ($role->id === 1) {
            return $user->hasRole($role->name);
        }

        return $user->can('roles.update');
    }

    public function delete(User $user, Role $role): bool
    {
        if ($role->id === 1) {
            return false;
        }

        return $user->can('roles.destroy');
    }

    public function restore(User $user, Role $role): bool
    {
        return false;
    }

    public function forceDelete(User $user, Role $role): bool
    {
        return false;
    }
}
```

> HR project extra: `update`/`delete` also pass when `$user->type` is in `access_all_type` (super admin types) via `AdminHelperTrait`.

---

## 9. Controller — `app/Http/Controllers/Admin/RoleController.php`

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserTypeEnum;
use App\Http\Requests\Admin\RoleRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Services\User\PermissionGroupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RoleController extends AdminController
{
    public function __construct(private readonly PermissionGroupService $permissionGroups)
    {
        parent::__construct();

        $this->setClass('role');
        $this->table = 'roles';
    }

    public function index(Request $request)
    {
        $perPage = $this->getLimitView($request->input('limit', $this->limit));

        return view('admin.roles.index', [
            'data' => Role::filter($request)->paginate($perPage),
            'route_create' => $this->checkPerm($this->table.'.create'),
            'route_edit' => $this->checkPerm($this->table.'.edit'),
        ]);
    }

    public function create()
    {
        return view('admin.roles.create', [
            'permissions' => $this->groupedPermissions(),
            'rolePermissions' => [],
            'roleNameOptions' => UserTypeEnum::rankedOptions(),
        ]);
    }

    public function store(RoleRequest $request)
    {
        $input = $request->validated();

        $role = Role::create([
            'name' => $input['name'],
            'display_name' => $input['display_name'],
            'description' => $input['description'] ?? null,
            'can_login_web' => (bool) ($input['can_login_web'] ?? false),
            'can_login_mobile' => (bool) ($input['can_login_mobile'] ?? true),
        ]);

        $role->syncPermissions($this->permissionsFrom($request));

        return redirect()->route('admin.roles.index')->with('success', __('Role created successfully'));
    }

    /** A read-only view of the role: what it holds, module by module. */
    public function show(Request $request, $id)
    {
        $role = Role::with('permissions')->findOrFail($id);

        $modules = $role->permissions
            ->sortBy('id')
            ->groupBy(fn (Permission $permission): string => PermissionGroupService::moduleOf($permission->name))
            ->sortKeys()
            ->map(fn ($permissions, $module): array => [
                'name' => getModuleTableModelName($module),
                'permissions' => $permissions->pluck('display_name')->all(),
            ])
            ->values();

        return view('admin.roles.show', [
            'role' => $role,
            'modules' => $modules,
            'totalPermissions' => Permission::count(),
        ]);
    }

    public function edit($id)
    {
        $role = Role::with('permissions')->findOrFail($id);

        return view('admin.roles.edit', [
            'role' => $role,
            'permissions' => $this->groupedPermissions(),
            'rolePermissions' => $role->permissions->pluck('id')->toArray(),
            'roleNameOptions' => UserTypeEnum::rankedOptions(),
        ]);
    }

    public function update(RoleRequest $request, int $id)
    {
        $role = Role::findOrFail($id);
        Gate::authorize('update', $role);

        $input = $request->validated();

        if ($role->id === 1) {
            $input['name'] = $role->name; // the owner role cannot be renamed
        }

        $role->update([
            'name' => $input['name'],
            'display_name' => $input['display_name'],
            'description' => $input['description'] ?? $role->description,
            'can_login_web' => (bool) ($input['can_login_web'] ?? $role->can_login_web),
            'can_login_mobile' => (bool) ($input['can_login_mobile'] ?? $role->can_login_mobile),
        ]);

        $role->syncPermissions($this->permissionsFrom($request));

        return redirect()->route('admin.roles.index')->with('success', __('Role updated successfully'));
    }

    public function destroy($id)
    {
        $role = Role::find($id);

        if (! $role || $role->id === 1) {
            return redirect()->route('admin.roles.index')->with('error', __('Role deleted failed'));
        }

        Gate::authorize('delete', $role);

        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', __('Role deleted successfully'));
    }

    /**
     * @return array{resources: list<array{key: string, category_name: string, permissions: array<int, string>}>, custom: list<array{id: int, name: string, label: string}>}
     */
    private function groupedPermissions(): array
    {
        $grouped = $this->permissionGroups->grouped();

        return [
            'resources' => $grouped['resources']
                ->map(fn ($items, string $module): array => [
                    'key' => $module,
                    'category_name' => getModuleTableModelName($module),
                    'permissions' => $items->pluck('display_name', 'id')->toArray(),
                ])
                ->values()
                ->toArray(),
            'custom' => $grouped['custom']
                ->map(fn (Permission $permission): array => [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'label' => $this->permissionGroups->customLabel($permission),
                ])
                ->toArray(),
        ];
    }

    private function permissionsFrom(Request $request)
    {
        return Permission::whereIn('id', (array) $request->input('permissions', []))->get();
    }
}
```

### Helpers / enum the controller relies on

- `getModuleTableModelName(string $table): string` — maps a table name to a readable module name (`'roles' => 'Role'`). Minimal version:

  ```php
  function getModuleTableModelName(string $table): string
  {
      $names = [
          'roles' => 'Role',
          'users' => 'User',
          // ...
      ];

      return $names[$table] ?? \Illuminate\Support\Str::headline(\Illuminate\Support\Str::singular($table));
  }
  ```

- `userName(string $type): string` — translated label for a role `name` (used in the index table). Can be replaced by `$model->label()`.
- `UserTypeEnum::rankedOptions()` — `['super_admin' => 'Super Admin', ...]` for the role **Name** select. If role names are free text in the other project, replace the select with a text input and drop `Rule::enum`.

---

## 10. Views

### `resources/views/admin/roles/index.blade.php`

```blade
@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Roles') }}
@stop
@section('content')
    @include('admin.roles.components.head-search')

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6 head-label"><h5 class="card-title">{{ __('Roles') }}</h5></div>
                <div class="col-md-6 dt-action-buttons text-end pt-3 pt-md-0">
                    @include('admin.layouts.components.page-head', ['table' => 'roles', 'create_label' => __('Create Role')])
                </div>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            @include('admin.errors.alerts')
            @include('admin.layouts.table-head')
            @include('admin.roles.components.table')
            @include('admin.layouts.table-foot')
            @include('admin.layouts.paginate-all')
        </div>
    </div>
@stop
@section('after_foot')
    @include('admin.layouts.filter')
    @include('admin.layouts.delete')
@stop
```

### `resources/views/admin/roles/create.blade.php`

```blade
@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Create Role') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.roles.index')])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.create', ['table' => 'roles'])
    @include('admin.roles.components.form', ['action' => 'Add'])
    @include('admin.layouts.forms.close')
@stop
```

### `resources/views/admin/roles/edit.blade.php`

```blade
@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Edit Role') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.roles.index')])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.edit', ['model' => $role, 'model_id' => $role->id, 'table' => $role->getTable()])
    @include('admin.roles.components.form', ['action' => 'Edit'])
    @include('admin.layouts.forms.close')
@stop
```

> `admin.layouts.forms.create` / `edit` / `close` open and close the `<form>` (POST to `admin.{table}.store`, or PUT to `admin.{table}.update` with model binding). Plain equivalent:
>
> ```blade
> <form method="POST" action="{{ isset($role) ? route('admin.roles.update', $role) : route('admin.roles.store') }}">
>     @csrf
>     @isset($role) @method('PUT') @endisset
>     ...
> </form>
> ```

### `resources/views/admin/roles/show.blade.php`

```blade
@extends('admin.layouts.app')
@section('title') {{ __('Show Role') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.roles.index')])
@stop
@section('content')
<div class="row">
    <div class="box">
        <div class="box-body">
            <div class="form-group">
                <label>{{ __('Name') }}</label>
                {{ $role->name }}
            </div>
            <div class="form-group">
                <label>{{ __('Display Name') }}</label>
                {{ $role->display_name }}
            </div>
            <div class="form-group">
                <label>{{ __('Description') }}</label>
                {{ $role->description }}
            </div>
            <div class="form-group">
                <label>{{ __('Permissions') }}</label>
                {{ $role->permissions->count() }} / {{ $totalPermissions }}
            </div>

            @if ($modules->isEmpty())
                <p class="text-muted">{{ __('No permissions') }}</p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{ __('Module') }}</th>
                                <th>{{ __('Permissions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($modules as $module)
                                <tr>
                                    <td>{{ $module['name'] }}</td>
                                    <td>
                                        @foreach ($module['permissions'] as $permission)
                                            <span class="badge bg-label-primary me-1">{{ __($permission) }}</span>
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@stop
```

### `resources/views/admin/roles/components/head-search.blade.php`

```blade
@include('admin.layouts.components.filter.header', [
    'filterKeys' => ['search', 'limit'],
])

    <div class="col-sm-6">
        <div class="box">
            <div class="box-body">
                @include('admin.layouts.forms.fields.text', [
                    'not_req' => true,
                    'text_name' => 'search',
                    'text_value' => request()->input('search') ?? null,
                ])
            </div>
        </div>
    </div>

@include('admin.layouts.components.filter.footer')
```

### `resources/views/admin/roles/components/table.blade.php`

```blade
<thead>
    <tr>
        <th>{{ __('Role') }}</th>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Web dashboard') }}</th>
        <th>{{ __('Mobile app') }}</th>
        <th>{{ __('Setting') }}</th>
    </tr>
</thead>
<tbody>
@foreach ($data as $key => $model)
    <tr>
        <td>{{ userName($model->name) }}</td>
        <td>{{ $model->display_name }}</td>
        @foreach (['can_login_web', 'can_login_mobile'] as $platformField)
            <td>
                @if ($model->{$platformField})
                    <span class="badge bg-label-success">{{ __('Allowed') }}</span>
                @else
                    <span class="badge bg-label-secondary">{{ __('Not allowed') }}</span>
                @endif
            </td>
        @endforeach
        <td>
            @include('admin.layouts.forms.dropdowns.dropdown', ['showEdit' => true])
        </td>
    </tr>
@endforeach
```

> `admin.layouts.forms.dropdowns.dropdown` = the row actions menu (show / edit / delete that opens the shared delete modal from `admin.layouts.delete`).

### `resources/views/admin/roles/components/form.blade.php`

```blade
<div class="col-12 mb-4">
    <div class="card role-card mb-4">
        <div class="card-body">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="row g-3">
                        <div class="col-md-6">
                            @include('admin.layouts.forms.fields.select', [
                                'label_name' => __('Name'),
                                'select_name' => 'name',
                                'select_function' => $roleNameOptions,
                            ])
                        </div>
                        <div class="col-md-6">
                            @include('admin.layouts.forms.texts.name', [
                                'name' => __('Display Name'),
                                'name_text' => 'display_name',
                                'not_req' => true,
                            ])
                        </div>
                        <div class="col-12">
                            @include('admin.layouts.forms.fields.textarea', [
                                'text_name' => 'description',
                                'label_name' => __('Description'),
                                'not_req' => true,
                            ])
                        </div>
                        <div class="col-12">
                            <label class="form-label d-block">{{ __('Users with this role can sign in on') }}</label>
                            @foreach (['can_login_web' => [__('Web dashboard'), false], 'can_login_mobile' => [__('Mobile app'), true]] as $platformField => [$platformLabel, $platformDefault])
                                <input type="hidden" name="{{ $platformField }}" value="0">
                                <label class="switch switch-success me-4">
                                    <input type="checkbox" class="switch-input" name="{{ $platformField }}" value="1" @checked(old($platformField, isset($role) ? $role->{$platformField} : $platformDefault))>
                                    <span class="switch-toggle-slider">
                                        <span class="switch-on"><i class="ti ti-check"></i></span>
                                        <span class="switch-off"><i class="ti ti-x"></i></span>
                                    </span>
                                    <span class="switch-label">{{ $platformLabel }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <label class="switch switch-primary mt-lg-4">
                        <input type="checkbox" class="switch-input" id="role-select-all">
                        <span class="switch-toggle-slider">
                            <span class="switch-on"><i class="ti ti-shield-check"></i></span>
                            <span class="switch-off"><i class="ti ti-shield"></i></span>
                        </span>
                        <span class="switch-label fw-medium">{{ __('Select All') }}</span>
                    </label>
                    <small class="d-block text-muted mt-1">{{ __('Enables/Disables all Permissions for this role') }}</small>
                </div>
            </div>
        </div>
    </div>

    @include('admin.roles.components.permissions-table', ['data' => $permissions])
</div>
```

> Plain-HTML replacements for the shared field partials, if the other project doesn't have them:
>
> ```blade
> <label class="form-label">{{ __('Name') }}</label>
> <select name="name" class="form-select select2" required>
>     @foreach ($roleNameOptions as $value => $label)
>         <option value="{{ $value }}" @selected(old('name', $role->name ?? null) === $value)>{{ $label }}</option>
>     @endforeach
> </select>
>
> <label class="form-label">{{ __('Display Name') }}</label>
> <input type="text" name="display_name" class="form-control" value="{{ old('display_name', $role->display_name ?? '') }}" required>
>
> <label class="form-label">{{ __('Description') }}</label>
> <textarea name="description" class="form-control">{{ old('description', $role->description ?? '') }}</textarea>
> ```

### `resources/views/admin/roles/components/permissions-table.blade.php`

Tabs (Resources / Custom), collapsible per-module cards, per-group "Select all / Deselect all", custom-permission search, and a global "Select All" switch synced with all checkboxes. No external JS needed.

```blade
<div class="card role-card role-permissions">
    <div class="card-header border-bottom pb-0">
        <ul class="nav nav-pills mb-3" role="tablist">
            <li class="nav-item" role="presentation">
                <button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#role-tab-resources" role="tab">
                    {{ __('Resources') }}
                    <span class="badge rounded-pill bg-label-warning ms-1">{{ count($data['resources']) }}</span>
                </button>
            </li>
            @if (! empty($data['custom']))
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#role-tab-custom" role="tab">
                        {{ __('Custom Permissions') }}
                        <span class="badge rounded-pill bg-label-warning ms-1">{{ count($data['custom']) }}</span>
                    </button>
                </li>
            @endif
        </ul>
    </div>

    <div class="card-body pt-4">
        <div class="tab-content p-0">
            <div class="tab-pane fade show active" id="role-tab-resources" role="tabpanel">
                @foreach ($data['resources'] as $resource)
                    <div class="card border shadow-none mb-4 role-permission-group">
                        <div class="card-header d-flex justify-content-between align-items-start cursor-pointer"
                            data-bs-toggle="collapse" data-bs-target="#role-resource-{{ $resource['key'] }}" aria-expanded="true">
                            <div>
                                <h6 class="mb-1">{{ __($resource['category_name']) }}</h6>
                                <small class="text-muted">{{ $resource['key'] }}</small>
                            </div>
                            <i class="ti ti-chevron-up role-collapse-icon"></i>
                        </div>
                        <div class="collapse show" id="role-resource-{{ $resource['key'] }}">
                            <div class="card-body border-top pt-3">
                                <a href="javascript:void(0)" class="role-group-toggle d-inline-block fw-medium mb-3"
                                    data-select="{{ __('Select all') }}" data-deselect="{{ __('Deselect all') }}">{{ __('Select all') }}</a>
                                <div class="row g-3">
                                    @foreach ($resource['permissions'] as $id => $permission)
                                        <div class="col-6 col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input role-permission" type="checkbox" name="permissions[]"
                                                    id="permission-{{ $id }}" value="{{ $id }}" @checked(in_array($id, $rolePermissions))>
                                                <label class="form-check-label" for="permission-{{ $id }}">{{ __(\Illuminate\Support\Str::headline($permission)) }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if (! empty($data['custom']))
                <div class="tab-pane fade role-permission-group" id="role-tab-custom" role="tabpanel">
                    <label class="form-label fw-medium" for="role-custom-search">{{ __('Custom permissions') }}</label>
                    <div class="input-group input-group-merge mb-3">
                        <span class="input-group-text"><i class="ti ti-search"></i></span>
                        <input type="search" class="form-control" id="role-custom-search" placeholder="{{ __('Start typing to search...') }}">
                    </div>
                    <a href="javascript:void(0)" class="role-group-toggle d-inline-block fw-medium mb-3"
                        data-select="{{ __('Select all') }}" data-deselect="{{ __('Deselect all') }}">{{ __('Select all') }}</a>
                    <div class="row g-3">
                        @foreach ($data['custom'] as $permission)
                            <div class="col-12 col-md-6 col-xl-3 role-custom-item" data-search="{{ strtolower($permission['label'].' '.$permission['name']) }}">
                                <div class="form-check">
                                    <input class="form-check-input role-permission" type="checkbox" name="permissions[]"
                                        id="permission-{{ $permission['id'] }}" value="{{ $permission['id'] }}" @checked(in_array($permission['id'], $rolePermissions))>
                                    <label class="form-check-label" for="permission-{{ $permission['id'] }}">{{ $permission['label'] }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    @include('admin.layouts.forms.buttons.form-save', ['btn_block' => 'md', 'save' => __('Save changes')])
    <a href="{{ route('admin.roles.index') }}" class="btn btn-label-secondary waves-effect">{{ __('Cancel') }}</a>
</div>

<style>
    .role-card { border-radius: 1rem; }
    .role-permissions .nav-pills .nav-link { color: var(--bs-body-color); }
    .role-permissions .nav-pills .nav-link.active { color: var(--bs-primary); background: rgba(var(--bs-primary-rgb), .08); box-shadow: none; }
    .role-permission-group > .card-header { padding-block: 1rem; }
    .role-permission-group > .card-header[aria-expanded="false"] .role-collapse-icon { transform: rotate(180deg); }
    .role-collapse-icon { transition: transform .2s ease; }
    .role-group-toggle { color: var(--bs-primary); }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('role-select-all');
        const permissions = Array.from(document.querySelectorAll('.role-permission'));
        const groups = Array.from(document.querySelectorAll('.role-permission-group'));

        // Checkboxes of a group, ignoring custom items hidden by the search box.
        const groupCheckboxes = (group) => Array.from(group.querySelectorAll('.role-permission'))
            .filter((checkbox) => !checkbox.closest('.role-custom-item.d-none'));

        const refresh = () => {
            groups.forEach((group) => {
                const toggle = group.querySelector('.role-group-toggle');
                if (!toggle) {
                    return;
                }
                const checkboxes = groupCheckboxes(group);
                const allChecked = checkboxes.length > 0 && checkboxes.every((checkbox) => checkbox.checked);
                toggle.textContent = allChecked ? toggle.dataset.deselect : toggle.dataset.select;
            });
            if (selectAll) {
                selectAll.checked = permissions.length > 0 && permissions.every((checkbox) => checkbox.checked);
            }
        };

        selectAll?.addEventListener('change', () => {
            permissions.forEach((checkbox) => checkbox.checked = selectAll.checked);
            refresh();
        });

        groups.forEach((group) => {
            group.querySelector('.role-group-toggle')?.addEventListener('click', () => {
                const checkboxes = groupCheckboxes(group);
                const check = !checkboxes.every((checkbox) => checkbox.checked);
                checkboxes.forEach((checkbox) => checkbox.checked = check);
                refresh();
            });
        });

        permissions.forEach((checkbox) => checkbox.addEventListener('change', refresh));

        document.getElementById('role-custom-search')?.addEventListener('input', (event) => {
            const term = event.target.value.trim().toLowerCase();
            document.querySelectorAll('.role-custom-item').forEach((item) => {
                item.classList.toggle('d-none', term !== '' && !item.dataset.search.includes(term));
            });
            refresh();
        });

        refresh();
    });
</script>
```

> `admin.layouts.forms.buttons.form-save` = a submit button. Plain equivalent: `<button type="submit" class="btn btn-primary">{{ __('Save changes') }}</button>`.

---

## 11. Seeding the permissions (`config/laratrust_seeder.php`)

The role form lists whatever is in the `permissions` table, so every new permission must be seeded. Format used in the HR project:

```php
return [
    'permissions_structure' => [
        // Model-backed modules → description = module table → grouped into a card.
        'resources' => [
            'roles' => ['index', 'show', 'store', 'update', 'destroy'],
            'users' => ['index', 'show', 'store', 'update', 'status', 'destroy'],
        ],
        // Stand-alone permissions → empty description → "Custom permissions" tab.
        // '' = permission named after the module itself (e.g. "dashboard").
        'custom' => [
            'dashboard' => [''],
            'reports' => ['attendance', 'payroll'],
        ],
    ],

    // Default roles: module => actions, or '*' for every permission.
    'roles_structure' => [
        'super_admin' => '*',
        'employee' => [
            'employees' => ['index', 'show'],
        ],
    ],
];
```

Map action → `display_name` when seeding so the card labels/order work:

| action (route) | display_name |
|---|---|
| `index` | `view` |
| `show` | `show` |
| `store` | `create` |
| `update` | `edit` |
| `status` | `status` |
| `destroy` | `delete` |
| anything else (`approve`, `reject`, `cancel`, ...) | same as action |

---

## 12. Translation keys used

`Roles`, `Create Role`, `Edit Role`, `Show Role`, `Role`, `Name`, `Display Name`, `Description`, `Permissions`, `Module`, `No permissions`, `Resources`, `Custom Permissions`, `Custom permissions`, `Select All`, `Select all`, `Deselect all`, `Enables/Disables all Permissions for this role`, `Users with this role can sign in on`, `Web dashboard`, `Mobile app`, `Allowed`, `Not allowed`, `Setting`, `Save changes`, `Cancel`, `Start typing to search...`, `Role created successfully`, `Role updated successfully`, `Role deleted successfully`, `Role deleted failed`, `Role has users`.

---

## 13. Porting checklist

1. Install Laratrust and run its migrations; point `config/laratrust.php` models to `App\Models\Role` / `App\Models\Permission`.
2. Add the login-platforms migration (optional).
3. Copy models, builder, service, request, policy, controller.
4. Register the policy if not auto-discovered: `Gate::policy(Role::class, RolePolicy::class);`
5. Add `getModuleTableModelName()` helper (and `userName()` or use `$role->label()`).
6. Replace `UserTypeEnum::rankedOptions()` with the other project's role-name source (or a free-text input).
7. Copy the views; swap the shared `admin.layouts.*` partials for the plain-HTML equivalents above if the other project doesn't have them.
8. Add the route + sidebar link, seed `roles.*` permissions, and give them to the admin role.

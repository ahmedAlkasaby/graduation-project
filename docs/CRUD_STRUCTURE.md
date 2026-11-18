# CRUD Module Structure

How one model's CRUD is built in this project, layer by layer, so the same
structure can be copied into another project.

The example module is **Holiday** (table `holidays`). To build a new module,
replace these names:

| Placeholder | Example | Used for |
|---|---|---|
| `{Model}` | `Holiday` | class names |
| `{table}` | `holidays` | table, routes, permissions, view folder |
| `{class}` | `holiday` | singular name (sidebar active menu) |

---

## 1. File checklist

```
app/
├── Models/{Model}.php                          # model (extends SiteModel)
├── Builders/{Model}Builder.php                 # query scopes (extends BaseBuilder)
├── Http/
│   ├── Requests/Admin/{Model}Request.php       # validation (shared by panel + API)
│   ├── Controllers/Admin/{Model}Controller.php # panel CRUD (Blade)
│   ├── Controllers/Api/{Model}Controller.php   # API CRUD (JSON)
│   └── Resources/
│       ├── {Model}Resource.php                 # one record as JSON
│       └── {Model}Collection.php               # list wrapper
└── Services/{Area}/{Model}Service.php          # only when there is business logic

database/
├── migrations/xxxx_create_{table}_table.php
├── migrations/xxxx_add_{table}_permissions.php
└── factories/{Model}Factory.php

resources/views/admin/{table}/
├── index.blade.php
├── create.blade.php
├── edit.blade.php
└── components/
    ├── form.blade.php
    ├── table.blade.php
    └── head-search.blade.php

routes/admin.php                                # Route::resource(...)
routes/api.php                                  # Route::apiResources([...])
config/laratrust_seeder.php                     # module permissions
app/Traits/AdminHelperTrait.php                 # register the module
app/Http/Controllers/Admin/AjaxStatusController.php  # only if it has an active switch
resources/views/admin/layouts/sidebar.blade.php # menu item
resources/lang/en.json + ar.json                # every new __('...') string
tests/Feature/{Model}ApiTest.php
```

### Artisan commands

```bash
php artisan make:model {Model} -mf --no-interaction        # model + migration + factory
php artisan make:class Builders/{Model}Builder --no-interaction
php artisan make:request Admin/{Model}Request --no-interaction
php artisan make:controller Admin/{Model}Controller --no-interaction
php artisan make:controller Api/{Model}Controller --no-interaction
php artisan make:resource {Model}Resource --no-interaction
php artisan make:resource {Model}Collection --collection --no-interaction
php artisan make:migration add_{table}_permissions --no-interaction
php artisan make:test --phpunit {Model}ApiTest --no-interaction
```

---

## 2. Database

### Migration

```php
Schema::create('holidays', function (Blueprint $table) {
    $table->id();
    $table->string('name');                         // translatable: stored as JSON {ar, en}
    $table->date('date_start');
    $table->date('date_end')->nullable();
    $table->tinyInteger('active')->default(1);      // every list module has active
    $table->tinyInteger('order_id')->nullable();    // and order_id (used by defaultOrder())
    $table->timestamps();
});
```

### Permissions migration

Permissions are named after routes: `{table}.{action}`.

```php
private const ACTIONS = ['index', 'show', 'store', 'update', 'status', 'destroy'];
private const MODULE = 'holidays';

public function up(): void
{
    $permissionIds = collect(self::ACTIONS)->map(fn (string $action): int => Permission::firstOrCreate(
        ['name' => self::MODULE.".{$action}"],
        ['description' => self::MODULE, 'display_name' => $action]
    )->id);

    Role::whereIn('name', ['super_admin', 'admin'])->get()->each(function (Role $role) use ($permissionIds): void {
        $role->permissions()->syncWithoutDetaching($permissionIds->all());
        $role->flushCache();
    });

    Permission::forgetRouteMap();   // the route → permission map is cached forever
}
```

Add the same list to `config/laratrust_seeder.php`:

```php
'holidays' => ['index', 'show', 'store', 'update', 'status', 'destroy'],
```

`create` and `edit` have no permission of their own: they map to `store` and
`update` (`Permission::FORM_ACTIONS`).

### Factory

```php
public function definition(): array
{
    $label = fake()->unique()->words(2, true);

    return [
        'name' => ['ar' => $label, 'en' => $label],   // the model cast stores it as JSON
        'date_start' => fake()->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
        'date_end' => null,
        'active' => 1,
    ];
}

public function inactive(): static
{
    return $this->state(fn () => ['active' => 0]);
}
```

---

## 3. Model + Builder

### Model — `app/Models/Holiday.php`

```php
class Holiday extends SiteModel          // SiteModel adds logging, activity log, attachments
{
    use HasFactory;

    public array $searchable = ['name']; // columns that the `search` filter looks in

    public function newEloquentBuilder($query): HolidayBuilder
    {
        return new HolidayBuilder($query);
    }

    protected $fillable = ['name', 'date_start', 'date_end', 'active', 'order_id'];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'date_start' => 'date',
            'date_end' => 'date',
        ];
    }
}
```

A model with no custom scopes returns `new BaseBuilder($query)` directly.

### Builder — `app/Builders/HolidayBuilder.php`

Query logic lives here, not in controllers.

```php
class HolidayBuilder extends BaseBuilder
{
    public function onDate(Carbon $date): static
    {
        return $this->where('active', 1)
            ->whereDate('date_start', '<=', $date->toDateString());
    }
}
```

Methods every builder gets from `BaseBuilder`:

| Method | What it does |
|---|---|
| `filter($request->only(self::FILTERS))` | applies `search`, `active` and other filters |
| `search($term)` | searches the model's `$searchable` columns |
| `active(1)` | active rows only |
| `defaultOrder()` | `order_id`, then newest first |
| `dateFilter($request)` | date range filter on `created_at` |
| `paginateRequested($request)` | paginates by the `limit` request param |

---

## 4. Validation — `app/Http/Requests/Admin/HolidayRequest.php`

One request class, used by both the panel and the API controller.

```php
class HolidayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;   // the route-name permission middleware already checked access
    }

    public function rules(): array
    {
        return [
            'name.ar' => 'required|string|max:255',
            'name.en' => 'required|string|max:255',
            'date_start' => 'required|date',
            'date_end' => 'nullable|date|after_or_equal:date_start',
            'active' => 'required|in:0,1',
            'order_id' => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.ar.required' => __('The Arabic name is required'),
            'name.en.required' => __('The English name is required'),
        ];
    }
}
```

For a unique column that ignores the current record on update:

```php
$id = $this->route('id') ?? $this->route('holiday');
Rule::unique('holidays')->ignore($id),
```

---

## 5. Admin panel (Blade)

### Controller — `app/Http/Controllers/Admin/HolidayController.php`

```php
class HolidayController extends AdminController
{
    private const FILTERS = ['search', 'active'];

    public function __construct()
    {
        parent::__construct();
        $this->setClass('holiday');    // sidebar active item ($activeClass)
        $this->setTable('holidays');   // routes, permissions, view folder
    }

    public function index(Request $request)
    {
        $perPage = $this->getLimitView($request->input('limit', $this->limit));

        $data = Holiday::query()
            ->filter($request->only(self::FILTERS))
            ->defaultOrder()
            ->paginate($perPage);

        $route_create = $this->checkPerm($this->table.'.create');   // shows the "Create" button
        $route_edit = $this->checkPerm($this->table.'.edit');       // shows "Edit" in the row menu

        return view('admin.holidays.index', compact('data', 'route_create', 'route_edit'))
            ->with('i', ($request->input('page', 1) - 1) * $perPage);
    }

    public function create()
    {
        return view('admin.holidays.create');
    }

    public function store(HolidayRequest $request)
    {
        Holiday::create($request->validated());

        return redirect()->route('admin.holidays.index')
            ->with('success', __('Holiday created successfully'));
    }

    public function edit($id)
    {
        $holiday = Holiday::find($id);
        if (empty($holiday)) {
            return $this->pageError();
        }

        return view('admin.holidays.edit', compact('holiday'));
    }

    public function update(HolidayRequest $request, $id)
    {
        $holiday = Holiday::find($id);
        if (empty($holiday)) {
            return $this->pageError();
        }

        $holiday->update($request->validated());

        return redirect()->route('admin.holidays.index')
            ->with('success', __('Holiday updated successfully'));
    }

    public function destroy($id)
    {
        $holiday = Holiday::find($id);
        if (empty($holiday)) {
            return $this->pageError();
        }
        $holiday->delete();

        return redirect()->route('admin.holidays.index')
            ->with('success', __('Holiday deleted successfully'));
    }
}
```

Rules:
- Select options and other view data are built in the controller (or an enum
  method) and passed to the view. No logic in Blade.
- If the module has business logic, the controller calls a Service and stays thin.

### Route — `routes/admin.php`

```php
Route::resource('holidays', HolidayController::class);
```

The `auto-check-permission` middleware checks the permission named after the
route (`holidays.index`, `holidays.store`, …), so the controller does no
permission checks of its own.

### Views — `resources/views/admin/holidays/`

**index.blade.php** — list page

```blade
@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title')
    {{ __('Holidays') }}
@stop
@section('content')
    @include('admin.holidays.components.head-search')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6 head-label">
                    <h5 class="card-title">{{ __('Holidays') }}</h5>
                </div>
                <div class="col-md-6 dt-action-buttons text-end pt-3 pt-md-0">
                    @include('admin.layouts.components.page-head', [
                        'table' => 'holidays',
                        'create_label' => __('Create Holiday'),
                    ])
                </div>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            @include('admin.errors.alerts')
            @include('admin.layouts.table-head')
            @include('admin.holidays.components.table')
            @include('admin.layouts.table-foot')
            @include('admin.layouts.paginate-all')
        </div>
    </div>
@stop
@section('after_foot')
    @include('admin.layouts.filter')
    @include('admin.layouts.status')
    @include('admin.layouts.delete')
@stop
```

**create.blade.php**

```blade
@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Create Holiday') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.holidays.index')])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.create', ['table' => 'holidays'])
    @include('admin.holidays.components.form', ['action' => 'Add'])
    @include('admin.layouts.forms.close')
@stop
```

**edit.blade.php**

```blade
@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Edit Holiday') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.holidays.index')])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.edit', [
        'model' => $holiday,
        'model_id' => $holiday->id,
        'table' => $holiday->getTable(),
    ])
    @include('admin.holidays.components.form', ['action' => 'Edit'])
    @include('admin.layouts.forms.close')
@stop
```

**components/form.blade.php** — fields shared by create and edit

```blade
@include('admin.layouts.forms.head', ['show_name' => true])   {{-- the name.ar / name.en inputs --}}
<div class="row g-3 mt-2 mb-4">
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.date', [
            'date_name' => 'date_start',
            'date_value' => isset($holiday) ? $holiday->date_start?->toDateString() : null,
            'label_name' => __('Start Date'),
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'order_id',
            'number_value' => isset($holiday) ? $holiday->order_id : 0,
            'label_name' => __('Order Id'),
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.selects.active')
    </div>
</div>
@include('admin.layouts.forms.footer')
```

Ready-made fields in `admin/layouts/forms/fields/`: `text`, `textarea`,
`number`, `date`, `date-time`, `select`, `multi_select`, `select-tags`,
`image`, `attachment`, `phone`, `password`, `color`, `hidden`.

**components/table.blade.php** — table rows

```blade
<thead>
    <tr>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Start Date') }}</th>
        <th>{{ __('Status') }}</th>
        <th>{{ __('Setting') }}</th>
    </tr>
</thead>
@foreach ($data as $key => $model)
    <tr>
        <td>{{ $model->name[$admin_language] ?? '' }}</td>
        <td>{{ $model->date_start?->toDateString() }}</td>
        <td>
            @include('admin.layouts.tables.status', [
                'active' => $model->active,
                'id' => $model->id,
                'route' => route('admin.'.$model->getTable().'.status'),
            ])
        </td>
        <td>
            @include('admin.layouts.forms.dropdowns.dropdown', ['showEdit' => true])
        </td>
    </tr>
@endforeach
```

The row dropdown also takes `'show' => true` and `'showDelete' => true`.

**components/head-search.blade.php** — filter box

```blade
@include('admin.layouts.components.filter.header', [
    'filterKeys' => ['search', 'active', 'limit'],
])
    <div class="col-sm-4">
        <div class="box"><div class="box-body">
            @include('admin.layouts.forms.fields.text', [
                'label_name' => __('Search (Name)'),
                'not_req' => true,
                'text_name' => 'search',
                'text_value' => request()->input('search') ?? null,
            ])
        </div></div>
    </div>
@include('admin.layouts.components.filter.footer')
```

### Sidebar — `resources/views/admin/layouts/sidebar.blade.php`

```blade
@can('holidays.index')
    <li class="menu-item @if ($activeClass == 'holiday') active @endif">
        <a href="{{ route('admin.holidays.index') }}" class="menu-link">
            <div>{{ __('Holidays') }}</div>
        </a>
    </li>
@endcan
```

Also add `auth()->user()->can('holidays.index')` to the parent group's `@if`,
so the group shows when the user can see at least one item in it.

### Active switch (optional)

If the list has an active/inactive toggle, add the module to
`AjaxStatusController::MODELS`:

```php
'holidays' => Holiday::class,
```

That registers `POST admin/holidays/status` (`admin.holidays.status`)
automatically.

### Register the module — `app/Traits/AdminHelperTrait.php`

```php
protected $admin_models = [..., 'holidays'];               // PermissionSeeder reads this
protected $admin_perms_models = [..., 'holidays.index'];
// soft-deletable modules also go in $admin_delete_models / $admin_restore_models
```

---

## 6. API (JSON)

### Controller — `app/Http/Controllers/Api/HolidayController.php`

```php
/** Company holidays. */
class HolidayController extends ApiHomeController
{
    private const FILTERS = ['search', 'active'];

    protected string $recordResource = HolidayResource::class;   // used by recordResponse()

    public function index(Request $request): JsonResponse
    {
        $query = Holiday::query()
            ->filter($request->only(self::FILTERS))
            ->defaultOrder();

        [$rows, $paginated] = $this->getDataWithPagination($query, $request);

        return $this->collectionResponse(HolidayResource::collection($rows), null, null, $paginated);
    }

    public function store(HolidayRequest $request): JsonResponse
    {
        $holiday = Holiday::create($request->validated());

        return $this->recordResponse($holiday, __('Holiday created successfully'), Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        return $this->recordResponse(Holiday::findOrFail($id));
    }

    public function update(HolidayRequest $request, int $id): JsonResponse
    {
        $holiday = Holiday::findOrFail($id);
        $holiday->update($request->validated());

        return $this->recordResponse($holiday, __('Holiday updated successfully'));
    }

    public function destroy(int $id): JsonResponse
    {
        Holiday::findOrFail($id)->delete();

        return $this->successResponse(null, __('Holiday deleted successfully'));
    }
}
```

Every API response uses the same envelope through the `ApiHomeController`
helpers: `recordResponse`, `collectionResponse`, `successResponse`.

### Resources

```php
class HolidayResource extends MainResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->translated($this->name),   // value in the request's language
            'date_start' => $this->date_start?->toDateString(),
            'date_end' => $this->date_end?->toDateString(),
            'order_id' => $this->order_id,
            'active' => (int) $this->active,
            'created_at' => $this->created_at?->timezone(config('app.timezone'))->toDateTimeString(),
        ];
    }
}

class HolidayCollection extends MainResourceCollection
{
    public function toArray($request)
    {
        return ['data' => $this->collection];
    }
}
```

### Routes — `routes/api.php` (inside the authenticated admin group)

```php
Route::apiResources([
    // ...
    'holidays' => HolidayController::class,
]);
```

If the create form needs options (dropdown lists), add the module to
`$formOptionControllers`. That registers `GET {table}/form-options`
(named `{table}.create`, guarded by `{table}.store`).

---

## 7. Translations

Every new `__('...')` string goes into **both** `resources/lang/en.json` and
`resources/lang/ar.json`:

```json
"Holidays": "العطلات",
"Create Holiday": "إضافة عطلة",
"Edit Holiday": "تعديل عطلة",
"Holiday created successfully": "تم إنشاء العطلة بنجاح",
"Holiday updated successfully": "تم تحديث العطلة بنجاح",
"Holiday deleted successfully": "تم حذف العطلة بنجاح"
```

---

## 8. Tests — `tests/Feature/HolidayApiTest.php`

```php
/** The dashboard's company holidays: list, create, show, edit and delete. */
class HolidayApiTest extends TestCase
{
    use DatabaseTransactions;

    public function test_store_creates_a_holiday(): void
    {
        $this->actingAsAdmin();

        $this->postJson(route('api.holidays.store'), $this->payload())
            ->assertCreated()
            ->assertJsonPath('message', __('Holiday created successfully'));
    }

    public function test_index_filters_inactive_holidays(): void
    {
        $this->actingAsAdmin();
        $inactive = Holiday::factory()->inactive()->create();

        $ids = collect($this->getJson(route('api.holidays.index', ['active' => 0, 'all_data' => 0]))
            ->assertOk()
            ->json('data.data'))->pluck('id');

        $this->assertContains($inactive->id, $ids);
    }

    private function actingAsAdmin(): void
    {
        Sanctum::actingAs(User::factory()->active()->create(['type' => 'super_admin', 'is_admin' => 1]), ['*']);
    }

    /** @return array<string, mixed> */
    private function payload(array $overrides = []): array
    {
        return [
            'name' => ['ar' => 'عيد الميلاد', 'en' => 'Christmas'],
            'date_start' => '2026-12-25',
            'date_end' => '2026-12-27',
            'active' => 1,
            'order_id' => 1,
            ...$overrides,
        ];
    }
}
```

Cover: list + filters, store, show, update, delete, validation errors, and
that a user without the permission is refused.

Run:

```bash
php artisan test --compact tests/Feature/HolidayApiTest.php
vendor/bin/pint --dirty --format agent
```

---

## 9. Order to build a new module

1. Migration + model + builder + factory → `php artisan migrate`
2. Permissions migration + `config/laratrust_seeder.php` + `AdminHelperTrait`
3. Form request
4. Admin controller + `Route::resource` + views + sidebar item
5. API controller + resources + `Route::apiResources`
6. `AjaxStatusController::MODELS` if the module has an active switch
7. Translations (en + ar)
8. Feature tests → run them → run Pint

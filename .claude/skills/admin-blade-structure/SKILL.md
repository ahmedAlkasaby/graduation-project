---
name: admin-blade-structure
description: The user's standard server-rendered admin panel (Blade) — layout, base controllers, route-name permissions, shared view variables, the reusable form/table/filter partials, status toggles, delete modals, AJAX dialogs, sidebar, and the per-module recipe (controller → FormRequest → views index/create/edit + components). Use when building or extending a Blade admin/dashboard module instead of (or next to) the JSON API, so every project's panel follows the same structure.
---

# Admin Blade Structure

The Blade counterpart of `backend-structure`. That skill covers the JSON API. This one covers the
server-rendered admin panel under `/admin`. Both share the same domain layer: Models, Builders,
Services, Actions and Enums. **Only the HTTP edge changes.** Controllers return views and
redirects instead of the JSON envelope.

Reference stack: Laravel 13, PHP 8.3, `spatie/laravel-html` ^3 (`html()` helper),
`santigarcor/laratrust` ^8 (roles/permissions), a Bootstrap 5 admin theme (Vuexy/Sneat-style
`assets/vendor/*`), jQuery, Parsley (client validation), Select2, flatpickr, Dropzone, TinyMCE, and Tabler icons (`ti ti-*`).

## How to use

- **New project** → copy the base layer (see *Porting checklist*), wire the middleware and the view composer, then add modules.
- **New module** → follow *Module recipe*. If the generator exists, start with `php artisan make:admin-crud Model --fields="..."` and adjust what it produces.
- **Existing project**: if `AdminController`, `MainController`, `AdminHelperTrait`, `AdminTrait` and `resources/views/admin/layouts/*` already exist, reuse them and never duplicate a partial. If the panel uses a different structure, ask before introducing this one.

## Request flow

```
Route (routes/admin.php, prefix "admin", name "admin.<table>.<action>")
  → Middleware group: admin, site.open, locale.admin, check.lastactive.admin, auto-check-permission
  → FormRequest (Http/Requests/Admin/<Model>Request)      validation errors → back()->withErrors()
  → Controller (Http/Controllers/Admin/<Model>Controller extends AdminController)
      → Builder ->filter($request, 'admin') / Service / Action   (same as the API)
  → view('admin.<table>.<screen>', [...])  or  redirect()->route(...)->with('success'|'error', __())
  → Layout admin.layouts.app + shared variables from the view composer
```

| Layer | Does | Never does |
|---|---|---|
| Controller | `setClass()`, build **every** select option/list, paginate, call Service, return view/redirect | business logic, building options inside Blade |
| FormRequest | rules + `messages()` with `__()` | DB writes |
| Module view (`index/create/edit`) | `@extends` the layout, `@include` partials with params | `@php` logic, queries, building option arrays |
| Module components (`components/form|table|head-search`) | compose shared partials for this module | duplicate a shared partial |
| Shared partials (`admin/layouts/**`) | generic field/table/form markup driven by params | know about a specific module |

**No logic in Blade.** Select options, filter lists, flags such as `route_create` and `route_edit`, and computed labels are built in the controller or in an enum/model method (`Country::listForSelect()`, `Enum::options()`), then passed to the view. Shared partials may use `@php` to assemble HTML attributes. Module views may not.

## Folder structure

```
app/Http/Controllers/
  MainController.php              settings: $limit, $admin_url, phone code/regex; uses AdminTrait, HelperTrait
  Admin/AdminController.php       base for every screen: 'admin' middleware, $this->user, setClass(), pageError()
  Admin/LoginAdminController.php  showLoginForm / login (throttle:5,1) / logout
  Admin/DashboardController.php   admin.index
  Admin/AjaxStatusController.php  one generic {module}/status endpoint for active switches
  Admin/<Model>Controller.php
app/Http/Requests/Admin/<Model>Request.php
app/Http/Middleware/
  AdminPanel.php                  signed in + has panel access, else admin.login / admin.unauthorized
  AutoCheckPermission.php         route name → permission, else admin.unauthorized
  LocaleAdmin.php                 app locale from user->locale, else settings('admin_language')
  CheckLastActiveAdmin.php, SiteOpen.php
app/Traits/AdminHelperTrait.php   $admin_models, $admin_delete_models, $admin_restore_models, $access_all_type, $language_array…
app/Traits/AdminTrait.php         checkPerm(), role/user option lists
app/Helpers/admin_navigation.php  getRouteName(), getModuleTableModelName()
app/Helpers/form_options.php      statusType(), perPageOptions(), other shared option lists
app/Providers/AppServiceProvider  registerViewComposers() → shared admin variables
app/Providers/RouteServiceProvider  Route::middleware('web')->group(base_path('routes/admin.php'))
routes/admin.php
resources/views/admin/
  layouts/        app.blade.php, meta, heads/{head,headForm}, foots/{foot,footForm,corejs},
                  sidebar, navbar, footer, pusher, filter, status, delete, paginate-all,
                  table-head, table-foot, components/{head,page-head,filter/{header,footer}},
                  forms/{create,edit,close,head,footer,fields/*,models/*,texts/*,buttons/*,dropdowns/*,…},
                  tables/{add,edit,show,delete-form,restore,status,…}
  errors/         404, unauthorized, alerts (lists), errors (forms)
  auth/login.blade.php
  dashboard/
  <table>/        index, create, edit, [show]
  <table>/components/  form, table, head-search, [modal, js]
public/assets/    theme vendor css/js (core.css per dir: ltr|rtl), public/js/admin/{en,ar}/*.js
```

Naming: the view folder, route resource and permission prefix all use the **table name** (`cities`). `setClass()` uses the **singular snake** name (`city`). The sidebar uses it to mark the active entry.

## Base layer

### AdminController

```php
class AdminController extends MainController
{
    use ValidatesRequests;

    public function __construct()
    {
        parent::__construct();
        $this->middleware('admin');
        $this->middleware(function (Request $request, $next) {
            $this->user = auth()->user();
            $this->language = $this->user->locale;
            $this->type = $this->user->type;
            return $next($request);
        });
    }

    protected function setClass($class): void { $this->class = $class; View::share('class', $class); }
    protected function setTable($table): void { $this->table = $table; View::share('table', $table); }
    protected function perPage($request = null, ?int $default = null): int { /* limit input or settings table_limit */ }
    public function pageError() { return view('admin.errors.404'); }
    public function pageUnauthorized() { return view('admin.errors.unauthorized'); }
}
```

`MainController::getLimitView($limit)` accepts only the values in `$limit_array_admin` and otherwise falls back to the `table_limit` setting.

### Shared view variables (view composer on `admin.*`)

Every admin view receives these, so never pass them yourself:
`company_name, admin_language, admin_dir (rtl|ltr), admin_theme_style (light|dark|semi-dark), admin_url, user_account, user_id, limit, table_limit, phone_regex, limit_array_admin, access_all_type, access_all_id, access_all_perms, admin_perms_models, admin_delete_models, admin_restore_models, user_admins`.
The sidebar and `image-head` also receive `siteLogo`. `class` (and `table`) come from `setClass()` / `setTable()`.

### Routes (`routes/admin.php`)

```php
$admin_url = 'admin';

Route::get($admin_url.'/login', [LoginAdminController::class, 'showLoginForm'])->name('admin.login');
Route::post($admin_url.'/login', [LoginAdminController::class, 'login'])->name('admin.login.save')->middleware('throttle:5,1');
Route::post($admin_url.'/logout', [LoginAdminController::class, 'logout'])->name('admin.logout');

Route::group([
    'prefix' => $admin_url,
    'as' => 'admin.',
    'middleware' => ['admin', 'site.open', 'locale.admin', 'check.lastactive.admin', 'auto-check-permission'],
], function () {
    Route::get('', [DashboardController::class, 'index'])->name('index');
    Route::get('404', [AdminController::class, 'pageError'])->name('404');
    Route::get('unauthorized', [AdminController::class, 'pageUnauthorized'])->name('unauthorized');

    foreach (AjaxStatusController::modules() as $module) {
        Route::post("{$module}/status", [AjaxStatusController::class, 'status'])
            ->defaults('module', $module)->name("{$module}.status");
    }

    Route::resource('cities', CityController::class);
    // Route::resource('x', XController::class)->only([...]);
});
```

Register the middleware aliases in `bootstrap/app.php`: `admin`, `locale.admin`, `check.lastactive.admin`, `site.open`, `auto-check-permission`.

### Permissions (route-name driven)

- Every admin route is named `admin.<table>.<action>`. `AutoCheckPermission` strips `admin.`, calls `Permission::nameForRoute($route)`, and redirects to `admin.unauthorized` if the user can't access it. Users whose type is in `$access_all_type` bypass the check.
- `create` is covered by `<table>.store` and `edit` by `<table>.update` (`Permission::FORM_ACTIONS`). A route with no matching permission is open to anyone signed in to the panel.
- Permission names: `<table>.index|show|store|update|status|destroy`. Each new permission also goes into `config/laratrust_seeder.php` and the module goes into `AdminHelperTrait::$admin_models`. The route map is cached, so call `Permission::forgetRouteMap()` after seeding.
- In the controller: `$this->checkPerm($this->table.'.create')` → pass it as `route_create` / `route_edit`. In Blade: `@can('<table>.index')`.

## Layout contract (`admin.layouts.app`)

```blade
@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Cities') }} @stop
@section('after_head') {{-- extra <head> tags --}} @stop
@section('head_content') {{-- header above content: back button --}} @stop
@section('content') ... @stop
@section('after_foot') {{-- page scripts / modals --}} @stop
```

- `head` / `foot` choose `admin.layouts.heads.<head>` / `admin.layouts.foots.<foot>`. The defaults are `head` / `foot`. Use `headForm` + `footForm` on any screen with forms, filters, select2, date pickers, dropzone or tinymce. Use `foots.corejs` for DataTables pages.
- `heads.head` offers `@yield('styles')`, and `footForm` offers `@yield('editorFiles')`.
- `<html dir>` and the theme CSS (`assets/vendor/css/{ltr|rtl}/core[-dark].css`) follow `admin_dir` / `admin_theme_style`.
- `<body>` / `.layout-wrapper` carry `data-url`, `data-language` and `data-user` for JS. CSRF is in `<meta name="_token">`.

## Module recipe (example: `cities`)

### Controller

```php
class CityController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->setClass('city');
        $this->table = 'cities';
    }

    public function index(Request $request)
    {
        $perPage = $this->getLimitView($request->input('limit', $this->limit));

        return view('admin.cities.index', [
            'data' => City::with('country')->filter($request, 'admin')->paginate($perPage),
            'route_create' => $this->checkPerm($this->table.'.create'),
            'route_edit' => $this->checkPerm($this->table.'.edit'),
        ]);
    }

    public function create()
    {
        return view('admin.cities.create', ['countries' => Country::listForSelect()]);
    }

    public function store(CityRequest $request)
    {
        City::create($request->validated());   // or $this->service->store(...)

        return redirect()->route('admin.cities.index')->with('success', __('City created successfully'));
    }

    public function show(Request $request, $id) { return $this->edit($id); }

    public function edit($id)
    {
        $city = City::find($id);
        if (! $city) {
            return $this->pageError();
        }

        return view('admin.cities.edit', ['city' => $city, 'countries' => Country::listForSelect()]);
    }

    public function update(CityRequest $request, $id) { /* find → pageError → update → redirect success */ }

    public function destroy($id)
    {
        $city = City::withCount('users')->find($id);
        if (! $city || $city->users_count > 0) {   // or $model->deletionBlocker()
            return redirect()->route('admin.cities.index')->with('error', __('City deleted failed'));
        }
        $city->delete();

        return redirect()->route('admin.cities.index')->with('success', __('City deleted successfully'));
    }
}
```

Rules: a missing record returns `$this->pageError()`, not `abort(404)`. Write actions always redirect to a named route with a flash key: `success`, `error`, `warning` or `info`. Logic beyond trivial CRUD goes in the module's Service, injected with `private readonly`.

### FormRequest (`Http/Requests/Admin/CityRequest.php`)

```php
public function authorize(): bool { return true; }   // permission already checked by route name

public function rules(): array
{
    return [
        'name' => ['required', 'array'],
        'name.en' => ['required', 'string', 'max:255'],
        'name.ar' => ['required', 'string', 'max:255'],
        'country_id' => ['required', 'integer', 'exists:countries,id'],
        'active' => ['required', 'boolean'],
    ];
}

public function messages(): array
{
    return ['name.en.required' => __('validation.required', ['attribute' => __('site.name')])];
}
```

Translatable columns are JSON and are posted as `name[en]` / `name[ar]` by `forms.head`.

### Views

`index.blade.php`
```blade
@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Cities') }} @stop
@section('content')
    @include('admin.cities.components.head-search')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6 head-label"><h5 class="card-title">{{ __('Cities') }}</h5></div>
                <div class="col-md-6 dt-action-buttons text-end pt-3 pt-md-0">
                    @include('admin.layouts.components.page-head', ['table' => 'cities', 'create_label' => __('Create City')])
                </div>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            @include('admin.errors.alerts')
            @include('admin.layouts.table-head')
            @include('admin.cities.components.table')
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

`create.blade.php` / `edit.blade.php`
```blade
@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Create City') }} @stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.cities.index')])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.create', ['table' => 'cities'])
    {{-- edit: @include('admin.layouts.forms.edit', ['model' => $city, 'model_id' => $city->id, 'table' => $city->getTable()]) --}}
    @include('admin.cities.components.form', ['action' => 'Add'])
    @include('admin.layouts.forms.close')
@stop
```

`components/form.blade.php`
```blade
@include('admin.layouts.forms.head', ['show_name' => true])   {{-- En/Ar stepper; opens the wizard --}}
<div class="content-header mb-3"></div>
@include('admin.layouts.forms.fields.select', [
    'select_name' => 'country_id',
    'label' => __('Country'),
    'select_function' => $countries,
    'select_value' => $city->country_id ?? null,
])
<div class="content-header mb-3"></div>
@include('admin.layouts.forms.fields.number', ['number_name' => 'order_id', 'label_name' => __('Order ID'), 'min' => 1, 'max' => 127, 'not_req' => true])
<div class="content-header mb-3"></div>
@include('admin.layouts.forms.fields.select', ['select_name' => 'active', 'label_name' => __('Status')])
<div class="content-header mb-3"></div>
@include('admin.layouts.forms.footer')   {{-- save button; closes the wizard --}}
```

`components/table.blade.php`
```blade
<thead class="border-top">
    <tr><th>{{ __('Name') }}</th><th>{{ __('Status') }}</th><th>{{ __('Setting') }}</th></tr>
</thead>
<tbody>
@foreach ($data as $model)
    <tr>
        <td>{{ $model->nameLang($admin_language) }}</td>
        <td>@include('admin.layouts.tables.status', ['active' => $model->active, 'ajax_class' => $model->getTable().'-status', 'id' => $model->id])</td>
        <td>@include('admin.layouts.forms.dropdowns.dropdown', ['showEdit' => true, 'showDelete' => true])</td>
    </tr>
@endforeach
</tbody>
```
The loop variable must be named `$model`, because the dropdown partial reads it.

`components/head-search.blade.php`
```blade
@include('admin.layouts.components.filter.header', ['filterKeys' => ['search', 'active', 'limit']])
    <div class="col-sm-4"><div class="box"><div class="box-body">
        @include('admin.layouts.forms.fields.text', ['not_req' => true, 'text_name' => 'search', 'text_value' => request('search')])
    </div></div></div>
    <div class="col-sm-4"><div class="box"><div class="box-body">
        @include('admin.layouts.forms.fields.select', [
            'not_req' => true, 'label' => __('Status'), 'select_name' => 'active',
            'select_value' => request('active'), 'select_function' => $statusOptions,   {{-- built in controller --}}
        ])
    </div></div></div>
@include('admin.layouts.components.filter.footer')   {{-- per-page select + Search/Reset; pass limit_filter to hide per-page --}}
```
The filter panel starts open whenever any of the `filterKeys` is in the query string. It submits GET to the current URL, and the Builder's `filter()` reads the same inputs.

## Shared partials reference

| Partial | Key params |
|---|---|
| `forms.create` | `table`, `table_parent?`, `model_id?`, `form_method` (POST), `form_status` (store), `form_validate` (yes → Parsley), `form_class`, `route_type`. Always multipart. |
| `forms.edit` | `model`, `table`, `parent_id?`/`table_parent?`, `form_method` (PATCH), `form_status` (update). Uses `html()->modelForm()`, so fields auto-fill from the model. |
| `forms.close` | closes either form |
| `forms.head` | En/Ar stepper: `show_name`, `show_title`, `show_value`, `show_content`, `show_address`, values `name_en`/`name_ar`…, `not_req` |
| `forms.footer` | save button; closes the stepper |
| `forms.fields.text` | `text_name` (name), `text_value`, `label_name`, `not_req`, `text_type` (parsley type), `placeholder`, `text_id`, `text_class`, `disable`, `read_only`, `date_type` (input type) |
| `forms.fields.textarea` | same pattern as text |
| `forms.fields.number` | `number_name`, `number_value`, `min`, `max`, `range`, `step`, `number_type`, `label`/`label_name`, `not_req` |
| `forms.fields.select` | `select_name` (active), `select_function` (options array, default `statusType()`), `select_value`, `label`/`label_name`, `is_multiple` (appends `[]`), `select_id`, `select_class`, `not_req`, `disable`. Always select2. |
| `forms.fields.{date,date-time,image,attachment,password,phone,color,icon,multi_select,select-tags}` | same `<x>_name` / `<x>_value` / `not_req` / `label_name` convention |
| `forms.fields.form-group-head/foot` + `label` | wrapper with `#<field>_wrap`, the server error under `#<field>_error`, and a `label_req` star |
| `forms.models.*`, `forms.texts.*`, `forms.users.*` | prebuilt common fields (country, role, latitude, email, password…) |
| `components.head` | back arrow: `back_url` |
| `components.page-head` | create button: `table`, `create_label`; shows only if `route_create` |
| `tables.add/edit/show/delete-form/restore` | `table`, `id`, `route_type`, `btn_class`, `ti_class`, `editName`/`deleteName` |
| `forms.dropdowns.dropdown` | row actions: needs `$model`, `showEdit` (+ `route_edit`), `showDelete` (table in `admin_delete_models` + `<table>.destroy`), `show`, `showRestore` |
| `tables.status` | active switch: `active`, `id`, `ajax_class` = `<table>-status`, `success_class`, `is_read` |
| `table-head` / `table-foot` | `<table id="datatables-basic">` wrapper: `datatable`, `table_limit` |
| `paginate-all` | `$data->appends(request()->except('page'))->links()` |
| `errors.alerts` / `errors.errors` | `$errors` list + flash `success`/`info`/`warning`/`error` |
| `filter`, `status`, `delete` | scripts/modals included in `after_foot` |

## Behaviors

### Active/status switch
1. Add `'<table>' => Model::class` to `AjaxStatusController::MODELS`. This registers the route `admin.<table>.status` automatically.
2. Add `'<table>': "{{ route('admin.<table>.status') }}"` to `statusRoutes` in `admin/layouts/status.blade.php`.
3. Give the model `updateActive($id, $status)`, which returns bool (from `SiteModel`). The JS posts `{_token, id, status}` and snaps the switch back unless the response is `true`.
4. Permission: `<table>.status`.

### Delete / restore
`tables.delete-form` renders an `#delete` link and a hidden DELETE form. `admin.layouts.delete` (in `after_foot`) shows the confirm modal and submits the matching `.delete-btn-submit[data-delete-id]`. The table must be listed in `$admin_delete_models` (and `$admin_restore_models` for soft-delete restore). The controller must refuse deletes that would orphan rows and redirect with `error`.

### AJAX dialog (edit in place, no edit page)
Use this when the index can edit a record in a modal:
- The modal partial is `components/<x>-modal.blade.php`. Its form is a normal `html()->form('PUT')->route(...)`, so it still works without JS.
- The controller's `update()` branches on `$request->expectsJson()`:
  ```php
  if ($request->expectsJson()) {
      return response()->json([
          'success' => true,
          'message' => __('... updated successfully'),
          'card' => view('admin.<table>.components.<x>-card', [...])->render(),   // re-rendered fragment
      ]);
  }
  return redirect()->route('admin.<table>.index')->with('success', __('...'));
  ```
- JS in `components/form-js.blade.php` (included in `after_foot`) submits with `$.ajax`, replaces the card with `card`, shows 422 `errors` inside the modal, and closes the modal on success.
- Without JS, a failed validation reopens the modal through `data-open-on-load`, which the controller computes. Remove the `edit` route (`->except(['edit'])`).

### Sidebar
Add one `<li class="menu-item">` inside the right group in `admin/layouts/sidebar.blade.php`, guarded by `@can('<table>.index')`. Mark it `active` when `$activeClass == '<class>'`, and add `<class>` to that group in `$menuGroups` so the group opens. Wrap the group in an `@if (auth()->user()->can(...) || ...)` covering all its children.

### Localization & RTL
Every string goes through `__()` with keys in `lang/{en,ar}`. A model's translated name is read with `$model->nameLang($admin_language)`. `ar` switches `dir="rtl"` and the RTL CSS, and per-language JS loads from `public/js/admin/{lang}/`.

## Generator

`php artisan make:admin-crud Model [--fields="code:string,price:integer,image:image,description:text,region_id:foreignId"] [--force]` builds the migration (when the table is new), model (extends `SiteModel`, wires the Builder), controller, FormRequest, the views (index/create/edit + components), the route and the status-toggle entry. Field types: `string→text`, `text→textarea`, `integer|float|decimal→number`, `image→dropzone`, `foreignId→select`, `date|datetime→picker`, `boolean→select`, `json→translatable`. After generating, review it against this guide, add permissions, the sidebar entry and tests.

## New module checklist

1. Model + migration + factory, Builder with `filter($request, 'admin')` (shared with the API).
2. `Http/Requests/Admin/<Model>Request`.
3. `Admin/<Model>Controller extends AdminController`: `setClass('<singular>')`, `$this->table = '<table>'`, and the actions. Build every option list here.
4. `Route::resource('<table>', ...)` in the admin group of `routes/admin.php`.
5. Views: `admin/<table>/{index,create,edit}.blade.php` + `components/{form,table,head-search}.blade.php`.
6. Permissions `<table>.*` → `config/laratrust_seeder.php` + `AdminHelperTrait::$admin_models` (and `$admin_delete_models` / `$admin_restore_models` if needed).
7. Status switch → `AjaxStatusController::MODELS` + `layouts/status.blade.php`.
8. Sidebar entry + `$menuGroups`.
9. Translation keys in `lang/en` and `lang/ar`.
10. Feature test: `$this->actingAs($admin)->get(route('admin.<table>.index'))->assertOk()`. Also cover store (redirect + `assertSessionHas('success')` + `assertDatabaseHas`), validation (`assertSessionHasErrors`), unauthorized (redirect to `admin.unauthorized`) and the delete blocker.
11. `vendor/bin/pint --dirty` and run the affected tests.

## Porting checklist (bringing the panel into another project)

Copy these, then adjust names, namespaces and settings:
- `app/Http/Controllers/MainController.php`, `Admin/{AdminController,LoginAdminController,DashboardController,AjaxStatusController}.php`
- `app/Http/Middleware/{AdminPanel,AutoCheckPermission,LocaleAdmin,CheckLastActiveAdmin,SiteOpen}.php` + their aliases in `bootstrap/app.php`
- `app/Traits/{AdminHelperTrait,AdminTrait,HelperTrait}.php`, `app/Helpers/{admin_navigation,form_options,labels}.php` (autoload them in `composer.json` → `autoload.files`)
- `Permission::nameForRoute()` / `routeMap()` / `forgetRouteMap()` on the Laratrust Permission model, plus `config/laratrust_seeder.php`
- `AppServiceProvider::registerViewComposers()` and a `settings()` helper/repository (`get()`, `getTableLimit()`, `getAdminUrl()`, `getPhoneRegex()`)
- `RouteServiceProvider`: load `routes/admin.php` with the `web` middleware, plus `routes/admin.php` itself
- `SiteModel` + its parent `SiteModelDefault` (`updateActive`, `nameLang`, `listForSelect`) and `BaseBuilder` (`filter`, `mainSearch`, `mainApplyDynamicFilters`, `defaultOrder`)
- `resources/views/admin/{layouts,errors,auth,dashboard}/**`
- `public/assets/**` (theme), `public/css/admin/parsley.css`, `public/js/admin/**`
- `composer require spatie/laravel-html santigarcor/laratrust` (ask before adding dependencies)
- Users table columns the panel reads: `locale`, `theme`, `type`, `active`, `last_active`

## Code style

Same as `backend-structure`: PHP 8.3, constructor promotion, `readonly` deps, explicit types, braces always, TitleCase enum cases, minimal comments. In Blade: 4-space indent, one `@include` per field separated by `<div class="content-header mb-3"></div>`, named routes only (`route('admin.x.index')`), no raw `<form>` (use `forms.create` / `forms.edit` / `html()`), and no `@php` in module views.

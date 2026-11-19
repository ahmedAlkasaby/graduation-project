<?php

namespace App\Providers;

use App\Enums\UserTypeEnum;
use App\Http\Middleware\AutoCheckPermission;
use App\Models\ActivityLog;
use App\Models\Attachment;
use App\Models\City;
use App\Models\Country;
use App\Models\Log;
use App\Models\Meta;
use App\Models\Region;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserCode;
use App\Models\UserDevice;
use App\Services\ActivityLogger;
use App\Services\AppSettingsService;
use App\Services\FirebaseService;
use App\Services\ModelChangeLogger;
use App\Traits\AdminHelperTrait;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(FirebaseService::class, fn () => new FirebaseService);

        // Caches the whole settings table, so one instance per request keeps
        // settings() to a single query no matter how often a view asks.
        $this->app->singleton(AppSettingsService::class, fn () => new AppSettingsService);

        // Both loggers memoise the user_devices row behind the request, so a
        // save touching ten columns still resolves the device once.
        $this->app->singleton(ModelChangeLogger::class);
        $this->app->singleton(ActivityLogger::class);
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);

        /*
        | Morph map: activity log and attachment rows store these short names
        | instead of class paths, so a model can be moved or renamed without
        | rewriting history.
        */
        Relation::morphMap([
            'users' => User::class,
            'user_codes' => UserCode::class,
            'roles' => Role::class,
            'settings' => Setting::class,
            'countries' => Country::class,
            'cities' => City::class,
            'regions' => Region::class,
            'activity_logs' => ActivityLog::class,
            'logs' => Log::class,
            'user_devices' => UserDevice::class,
            'metas' => Meta::class,
            'attachments' => Attachment::class,
            'notifications' => DatabaseNotification::class,
        ]);

        $this->registerUserValidators();
        $this->registerRoutePermissionMacro();
        $this->registerViewComposers();
    }

    /**
     * What every Blade admin view reads: the panel language and direction,
     * the signed in admin's theme, the logo and the shared admin lists.
     */
    protected function registerViewComposers(): void
    {
        // Every @include of an admin partial runs this, so the data is worked
        // out once per request and kept on the request.
        View::composer('admin.*', function (ViewContract $view): void {
            $attributes = request()->attributes;

            if (! $attributes->has('admin_view_data')) {
                $attributes->set('admin_view_data', $this->adminViewData());
            }

            $view->with($attributes->get('admin_view_data'));
        });
    }

    /**
     * @return array<string, mixed>
     */
    protected function adminViewData(): array
    {
        $settings = settings();
        $user = Auth::guard('web')->user();
        $helper = new class
        {
            use AdminHelperTrait;

            /** @return array<string, mixed> */
            public function lists(): array
            {
                return [
                    'limit_array_admin' => $this->limit_array_admin,
                    'access_all_type' => $this->access_all_type,
                    'access_all_id' => $this->access_all_id,
                    'access_all_perms' => $this->access_all_perms,
                    'admin_perms_models' => $this->admin_models,
                    'admin_delete_models' => $this->admin_delete_models,
                    'admin_restore_models' => $this->admin_restore_models,
                    'user_admins' => $this->user_admins,
                ];
            }
        };

        $language = app()->getLocale();
        $theme = in_array($user?->theme, ['light', 'dark', 'semi-dark'], true) ? $user->theme : 'light';
        $logo = $settings->get('logo_image');

        return [
            'company_name' => $settings->get('site_title') ?: config('app.name'),
            'admin_language' => $language,
            'admin_dir' => $language === 'ar' ? 'rtl' : 'ltr',
            'admin_theme_style' => $theme,
            'admin_url' => $settings->getAdminUrl(),
            'user_account' => $user,
            'user_id' => $user?->id,
            'limit' => $settings->getTableLimit(),
            'table_limit' => $settings->getTableLimit(),
            'phone_regex' => $settings->getPhoneRegex(),
            'siteLogo' => $logo && upload_exists($logo) ? upload_url($logo) : null,
        ] + $helper->lists();
    }

    /**
     * `->permission('cities.export')` on an admin route: replaces the group's
     * route-name check with these permissions, so only one check runs.
     */
    protected function registerRoutePermissionMacro(): void
    {
        Route::macro('permission', function (string ...$permissions): Route {
            /** @var Route $this */
            return $this->withoutMiddleware(AutoCheckPermission::ALIAS)
                ->middleware(AutoCheckPermission::ALIAS.':'.implode(',', $permissions));
        });
    }

    /**
     * Uniqueness for users is scoped by audience: an app user (donor or
     * volunteer) and an admin may share a phone number, two admins may not.
     */
    protected function registerUserValidators(): void
    {
        $adminOnly = fn ($query) => $query->whereIn('type', UserTypeEnum::adminValues());
        $clientOnly = fn ($query) => $query->whereIn('type', UserTypeEnum::appValues());

        $rules = [
            'uniqueUserName' => ['full_name', $adminOnly, false],
            'uniqueUserNameUpdate' => ['full_name', $adminOnly, true],
            'uniqueUserPhone' => ['phone', $adminOnly, false],
            'uniqueUserPhoneUpdate' => ['phone', $adminOnly, true],
            'uniqueUserEmail' => ['email', $adminOnly, false],
            'uniqueUserEmailUpdate' => ['email', $adminOnly, true],
            'uniqueUserClientPhone' => ['phone', $clientOnly, false],
            'uniqueUserClientPhoneUpdate' => ['phone', $clientOnly, true],
            'uniqueUserClientEmail' => ['email', $clientOnly, false],
            'uniqueUserClientEmailUpdate' => ['email', $clientOnly, true],
        ];

        foreach ($rules as $rule => [$column, $scope, $isUpdate]) {
            Validator::extend($rule, function ($attribute, $value, $parameters) use ($column, $scope, $isUpdate) {
                $query = User::query();
                $scope($query);

                if ($column === 'email') {
                    $value = strtolower((string) $value);
                }

                if ($isUpdate && isset($parameters[0])) {
                    $query->where('id', '!=', $parameters[0]);
                }

                return $query->where($column, $value)->count() === 0;
            });
        }
    }
}

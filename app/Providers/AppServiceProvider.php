<?php

namespace App\Providers;

use App\Models\ActivityLog;
use App\Models\Attachment;
use App\Models\City;
use App\Models\Country;
use App\Models\Log;
use App\Models\Meta;
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
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    use AdminHelperTrait;

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
        Paginator::useBootstrap();
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
            'activity_logs' => ActivityLog::class,
            'logs' => Log::class,
            'user_devices' => UserDevice::class,
            'metas' => Meta::class,
            'attachments' => Attachment::class,
            'notifications' => DatabaseNotification::class,
        ]);

        $this->registerUserValidators();
        $this->registerViewComposers();
    }

    /**
     * Uniqueness for users is scoped by audience: a client and an admin may
     * share a phone number, two admins may not.
     */
    protected function registerUserValidators(): void
    {
        $adminOnly = fn ($query) => $query->where('is_admin', 1);
        $clientOnly = fn ($query) => $query->where('is_client', 1);

        $rules = [
            'uniqueUserName' => ['name', $adminOnly, false],
            'uniqueUserNameUpdate' => ['name', $adminOnly, true],
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

    protected function registerViewComposers(): void
    {
        $languages = $this->language_array;

        view()->composer(['admin.*'], function ($view) use ($languages) {
            $settings = settings();

            $admin_language = $settings->get('admin_language', 'en');
            $admin_theme_style = 'light';
            $user_account = null;
            $user_id = 0;

            if (auth()->check()) {
                $user_account = auth()->user();
                $admin_language = $user_account->locale ?: $admin_language;
                $admin_theme_style = $user_account->theme ?: 'light';
                $user_id = $user_account->id;
            }

            $admin_language = in_array($admin_language, $languages) ? $admin_language : 'en';

            $view->with([
                'company_name' => $settings->get('site_title', config('app.name')),
                'admin_language' => $admin_language,
                'admin_dir' => $admin_language === 'ar' ? 'rtl' : 'ltr',
                'admin_theme_style' => $admin_theme_style,
                'admin_url' => $settings->getAdminUrl(),
                'user_account' => $user_account,
                'user_id' => $user_id,
                'limit' => $settings->getTableLimit(),
                'table_limit' => $settings->getTableLimit(),
                'phone_regex' => $settings->getPhoneRegex(),
                'limit_array_admin' => $this->limit_array_admin,
                'access_all_type' => $this->access_all_type,
                'access_all_id' => $this->access_all_id,
                'access_all_perms' => $this->access_all_perms,
                'admin_perms_models' => $this->admin_perms_models,
                'admin_delete_models' => $this->admin_delete_models,
                'admin_restore_models' => $this->admin_restore_models,
                'user_admins' => $this->user_admins,
            ]);
        });

        // The sidebar needs $siteLogo too: it shows the site title only when there is no logo.
        view()->composer(['admin.layouts.image-head', 'admin.layouts.sidebar'], function ($view) {
            $logo = settings()->get('logo_image');

            $view->with('siteLogo', $logo ? asset(trim($logo, "'")) : null);
        });
    }
}

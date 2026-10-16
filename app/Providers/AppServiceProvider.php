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
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
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
            'activity_logs' => ActivityLog::class,
            'logs' => Log::class,
            'user_devices' => UserDevice::class,
            'metas' => Meta::class,
            'attachments' => Attachment::class,
            'notifications' => DatabaseNotification::class,
        ]);

        $this->registerUserValidators();
    }

    /**
     * Uniqueness for users is scoped by audience: a client and an admin may
     * share a phone number, two admins may not.
     */
    protected function registerUserValidators(): void
    {
        $adminOnly = fn ($query) => $query->where('is_admin', 1);
        $clientOnly = fn ($query) => $query->where('is_admin', 0);

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

<?php

namespace App\Providers;

use App\Models\Role;
use App\Models\User;
use App\Policies\RolePolicy;
use App\Traits\AdminHelperTrait;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    use AdminHelperTrait;

    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Role::class => RolePolicy::class,

    ];


    public function boot()
    {
        $this->registerPolicies();

        Gate::define('viewPulse', function (User $user) {
            return in_array($user->type, $this->access_all_type);
        });
    }
}

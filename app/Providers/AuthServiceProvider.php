<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // A super admin skips every permission check (see AutoCheckPermission),
        // so `@can` and `->can()` answer yes for them too.
        Gate::before(fn (User $user): ?bool => $user->isSuperAdmin() ? true : null);
    }
}

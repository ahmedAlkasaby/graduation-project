<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class UserObserver
{
    public function created(User $user): void
    {
        $this->forgetCounts();
    }

    public function deleted(User $user): void
    {
        $this->forgetCounts();
    }

    private function forgetCounts(): void
    {
        Cache::forget('user_count');
        Cache::forget('client_count');
    }
}

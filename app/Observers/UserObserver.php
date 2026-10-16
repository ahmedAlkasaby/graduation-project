<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class UserObserver
{
    /**
     * `name` is what the rest of the app reads, but the form edits the two
     * parts, so it is composed here rather than in each controller.
     */
    public function saving(User $user): void
    {
        $parts = trim("{$user->name_first} {$user->name_last}");

        if ($parts !== '') {
            $user->name = $parts;
        }
    }

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

<?php

namespace App\Observers;

use App\Models\Setting;

class SettingObserver
{
    public function saved(Setting $setting): void
    {
        settings()->refresh();
    }

    public function deleted(Setting $setting): void
    {
        settings()->refresh();
    }
}

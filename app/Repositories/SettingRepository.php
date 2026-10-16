<?php

namespace App\Repositories;

use App\Models\Setting;

class SettingRepository
{
    public function getValue(string $key, $default = null)
    {
        return Setting::where('key', $key)->value('value') ?? $default;
    }

    public function getMany(array $keys): array
    {
        return Setting::whereIn('key', $keys)
            ->pluck('value', 'key')
            ->toArray();
    }
    public function checkOptions(string $option): string
    {
        $option_value = 0;
        $get_option_value = $this->getValue($option);
        if ($get_option_value == "yes") {
            $option_value = 1;
        } 
        return $option_value;

    }
}

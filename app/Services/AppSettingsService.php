<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Site settings, read from the `settings` table and cached whole.
 *
 * Registered as a singleton and reached through the settings() helper, so a
 * request that reads twenty settings still runs one query. Anything that
 * writes a setting must call refresh().
 */
class AppSettingsService
{
    protected string $cacheKey = 'app_settings';

    /**
     * The settings for the current request.
     *
     * The service is a singleton, so this memo lives exactly as long as one
     * request (or one console command). Without it every settings() read
     * repeats Schema::hasTable() and a cache round-trip -- measured at 37 of
     * each per admin request. Cross-request freshness is unchanged: it is
     * still governed by the cache entry below and cleared by refresh().
     */
    protected ?array $settings = null;

    public function get(string $key, $default = null)
    {
        return $this->load()[$key] ?? $default;
    }

    public function all(): array
    {
        return $this->load();
    }

    public function refresh(): array
    {
        $this->settings = null;

        Cache::forget($this->cacheKey);

        return $this->load();
    }

    public function getTableLimit(): int
    {
        return (int) ($this->get('table_limit') ?: 50);
    }

    public function getSiteUrl(): string
    {
        return (string) ($this->get('site_url') ?: url(''));
    }

    public function getAdminUrl(): string
    {
        return (string) ($this->get('admin_url') ?: 'admin');
    }

    public function getPhoneCode(): string
    {
        return (string) ($this->get('phone_code') ?: '20');
    }

    public function getPhoneRegex(): string
    {
        return (string) ($this->get('phone_regex') ?: '^01[0-2,5][0-9]{8}$');
    }

    public function isSiteOpen(): bool
    {
        return $this->get('site_open') !== 'no';
    }


    protected function load(): array
    {
        if ($this->settings !== null) {
            return $this->settings;
        }

        try {
            $cached = Cache::get($this->cacheKey);

            if (is_array($cached)) {
                return $this->settings = $cached;
            }

            if (! Schema::hasTable('settings')) {

                return [];
            }

            $fresh = Setting::query()->pluck('value', 'key')->toArray();

            Cache::forever($this->cacheKey, $fresh);

            return $this->settings = $fresh;
        } catch (Throwable) {

            return [];
        }
    }
}

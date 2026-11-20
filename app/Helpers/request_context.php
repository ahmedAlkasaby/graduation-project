<?php

use App\Services\AppSettingsService;



if (! function_exists('settings')) {

    function settings(): AppSettingsService
    {
        return app(AppSettingsService::class);
    }
}

if (! function_exists('getCurrentUserId')) {
    /** The signed-in user, whether they came through the API or the web guard. */
    function getCurrentUserId(): ?int
    {
        return auth('sanctum')->id()
            ?? auth()->guard('web')->id();
    }
}

if (! function_exists('defaultOption')) {
    /** A leading "choose one" row for a required select. */
    function defaultOption(?string $label = null): array
    {
        return [null => $label ?? __('site.select_option')];
    }
}

if (! function_exists('filterOption')) {
    function filterOption(?string $label = null): array
    {
        return [-1 => $label ?? __('None')];
    }
}

if (! function_exists('upload_url')) {

    function upload_url(?string $path): ?string
    {
        return app(App\Services\ImageHandlerService::class)->url($path);
    }
}

if (! function_exists('upload_exists')) {
    function upload_exists(?string $path): bool
    {
        return app(App\Services\ImageHandlerService::class)->exists($path);
    }
}

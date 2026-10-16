<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Laratrust\Models\Permission as LaratrustPermission;

class Permission extends LaratrustPermission
{
    protected $fillable = [
        'name', 'display_name', 'description',
    ];


    public const ROUTE_MAP_CACHE_KEY = 'permissions:route-map';


    protected static ?array $routeMap = null;

    protected static function booted(): void
    {

        static::saved(fn () => static::forgetRouteMap());
        static::deleted(fn () => static::forgetRouteMap());
    }


    public static function nameForRoute(string $route): ?string
    {
        return static::routeMap()[$route] ?? null;
    }


    public static function forRoute(string $route): ?self
    {
        $name = static::nameForRoute($route);

        return $name === null ? null : static::query()->where('name', $name)->first();
    }


    public static function routeMap(): array
    {
        return static::$routeMap ??= Cache::rememberForever(
            static::ROUTE_MAP_CACHE_KEY,
            function () {
                $map = [];

                foreach (static::query()->pluck('name') as $name) {
                    foreach (explode(',', (string) $name) as $route) {
                        $route = trim($route);

                        if ($route !== '') {
                            $map[$route] = $name;
                        }
                    }
                }

                return $map;
            }
        );
    }

    public static function forgetRouteMap(): void
    {
        static::$routeMap = null;

        Cache::forget(static::ROUTE_MAP_CACHE_KEY);
    }

    public function label(): string
    {
        return $this->display_name ?: $this->name;
    }
}

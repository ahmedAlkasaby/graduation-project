<?php

namespace Tests\Feature\Api\V1\Admin;

use App\Http\Middleware\AutoCheckPermission;
use App\Models\Permission;
use Database\Seeders\LaratrustSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * AutoCheckPermission refuses an admin route no permission claims, so a route
 * added without its permission in config/laratrust_seeder.php would be unreachable for
 * every admin but the super admin. This catches that at test time.
 */
class RoutePermissionsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Admin routes a guest has to reach, so they sit outside the auth and
     * permission group on purpose. Keep this list as short as it is.
     *
     * @var list<string>
     */
    private const GUEST_ROUTES = ['api.v1.admin.login'];

    /**
     * @return Collection<int, Route>
     */
    private function routesNamed(string $prefix): Collection
    {
        return collect(app(Router::class)->getRoutes()->getRoutes())
            ->filter(fn (Route $route) => str_starts_with((string) $route->getName(), $prefix))
            ->reject(fn (Route $route) => in_array($route->getName(), self::GUEST_ROUTES, true))
            ->values();
    }

    /**
     * The permission middleware entries a route actually runs, after
     * `withoutMiddleware`: "App\...\AutoCheckPermission" or "...:cities.export".
     *
     * @return list<string>
     */
    private function permissionMiddleware(Route $route): array
    {
        return collect(app(Router::class)->gatherRouteMiddleware($route))
            ->filter(fn ($middleware) => is_string($middleware) && Str::before($middleware, ':') === AutoCheckPermission::class)
            ->values()
            ->all();
    }

    #[Test]
    public function every_admin_route_runs_the_permission_check_exactly_once(): void
    {
        $routes = $this->routesNamed('api.v1.admin.');

        $this->assertNotEmpty($routes);

        $routes->each(fn (Route $route) => $this->assertCount(
            1,
            $this->permissionMiddleware($route),
            "{$route->getName()} must run the permission check exactly once."
        ));
    }

    #[Test]
    public function every_admin_route_is_claimed_by_a_seeded_permission(): void
    {
        $this->seed(LaratrustSeeder::class);

        $seeded = Permission::query()->pluck('name')->all();

        $unclaimed = $this->routesNamed('api.v1.admin.')
            ->reject(function (Route $route) use ($seeded) {
                $middleware = $this->permissionMiddleware($route)[0] ?? '';

                if (Str::contains($middleware, ':')) {
                    return array_diff(explode(',', Str::after($middleware, ':')), $seeded) === [];
                }

                return Permission::nameForRoute(AutoCheckPermission::permissionRouteName($route->getName())) !== null;
            })
            ->map(fn (Route $route) => $route->getName())
            ->all();

        $this->assertSame([], $unclaimed, 'Admin routes with no permission: '.implode(', ', $unclaimed));
    }

    #[Test]
    public function every_admin_route_requires_an_authenticated_admin(): void
    {
        $this->routesNamed('api.v1.admin.')->each(fn (Route $route) => $this->assertEmpty(
            array_diff(['auth:sanctum', 'admin'], $route->gatherMiddleware()),
            "{$route->getName()} is missing admin middleware."
        ));
    }

    #[Test]
    public function guest_routes_are_only_rate_limited(): void
    {
        foreach (self::GUEST_ROUTES as $name) {
            $route = app(Router::class)->getRoutes()->getByName($name);

            $this->assertNotNull($route, "{$name} is listed as a guest route but does not exist.");
            $this->assertSame([], $this->permissionMiddleware($route));
            $this->assertNotContains('auth:sanctum', $route->gatherMiddleware());
            $this->assertContains('throttle:login', $route->gatherMiddleware());
        }
    }

    #[Test]
    public function public_routes_are_never_permission_checked(): void
    {
        $this->routesNamed('api.v1.public.')->each(fn (Route $route) => $this->assertSame(
            [],
            $this->permissionMiddleware($route),
            "{$route->getName()} is permission checked but sits in the public context."
        ));
    }
}

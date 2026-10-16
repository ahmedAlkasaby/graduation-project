<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        /*
        |--------------------------------------------------------------------------
        | Global Middleware
        |--------------------------------------------------------------------------
        */
        $middleware->append([
            \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
            \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
            \App\Http\Middleware\TrimStrings::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
            \App\Http\Middleware\HttpsProtocol::class,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Middleware Groups
        |--------------------------------------------------------------------------
        */
        $middleware->group('web', [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        $middleware->group('api', [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Route Middleware Aliases
        |--------------------------------------------------------------------------
        */
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
            'can' => \Illuminate\Auth\Middleware\Authorize::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
            'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,


            // Custom
            'access.all' => \App\Http\Middleware\AccessAll::class,
            'admin' => \App\Http\Middleware\AdminPanel::class,
            'admin.trans' => \App\Http\Middleware\AdminPanelTrans::class,
            'check.lastactive' => \App\Http\Middleware\CheckLastActive::class,
            'check.lastactive.admin' => \App\Http\Middleware\CheckLastActiveAdmin::class,
            'locale' => \App\Http\Middleware\Locale::class,
            'locale.admin' => \App\Http\Middleware\LocaleAdmin::class,
            'site.open' => \App\Http\Middleware\SiteOpen::class,
            'app.open' => \App\Http\Middleware\AppOpen::class,
            'auto-check-permission' => \App\Http\Middleware\AutoCheckPermission::class,
            'cors' => \App\Http\Middleware\Cors::class,
            'json.response' => \App\Http\Middleware\ForceJsonResponse::class,
            'check.socialLogin' => \App\Http\Middleware\CheckSocialLoginAccess::class,
            'check.client' => \App\Http\Middleware\EnsureUserIsClient::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {


        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson()
        );


        $exceptions->dontReport([
            NotFoundHttpException::class,
            TokenMismatchException::class,
        ]);

        $json = function (string $status, string $message, int $code, array $extra = []) {
            return response()->json(['status' => $status, 'message' => $message] + $extra, $code);
        };

        $exceptions->render(function (ValidationException $e, Request $request) use ($json) {
            if (! $request->is('api/*') && ! $request->expectsJson()) {
                return null; // let the web flow redirect back with the error bag
            }

            return $json('error', __('validation_error'), 422, ['errors' => $e->errors()]);
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) use ($json) {
            if (! $request->is('api/*') && ! $request->expectsJson()) {
                return null;
            }

            return $json('unauthenticated', __('Please login'), 401);
        });

        $exceptions->render(function (AuthorizationException $e, Request $request) use ($json) {
            if (! $request->is('api/*') && ! $request->expectsJson()) {
                return null;
            }

            return $json('unauthorized', __("You don't have permission to do this"), 403);
        });

        $exceptions->render(function (ModelNotFoundException|NotFoundHttpException $e, Request $request) use ($json) {
            if (! $request->is('api/*') && ! $request->expectsJson()) {
                return null;
            }

            return $json('error', __('Not found'), 404);
        });


        $exceptions->render(function (Throwable $e, Request $request) use ($json) {
            if (! $request->is('api/*') && ! $request->expectsJson()) {
                return null;
            }

            if ($e instanceof HttpExceptionInterface) {
                return null; // 405, 429 and friends already carry a sensible status
            }

            $reference = (string) Str::uuid();

            Log::error('Unhandled API exception', [
                'reference' => $reference,
                'exception' => $e::class,
                'message' => $e->getMessage(),
                'path' => $request->path(),
            ]);

            return $json(
                'error',
                config('app.debug') ? $e->getMessage() : __('Server error'),
                500,
                ['reference' => $reference],
            );
        });
    })
    ->create();

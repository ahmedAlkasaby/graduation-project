<?php

use App\Http\Middleware\AccessAll;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\AutoCheckPermission;
use App\Http\Middleware\CheckLastActive;
use App\Http\Middleware\CheckLastActiveAdmin;
use App\Http\Middleware\CheckSocialLoginAccess;
use App\Http\Middleware\Cors;
use App\Http\Middleware\EncryptCookies;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\EnsureUserIsClient;
use App\Http\Middleware\ForceJsonResponse;
use App\Http\Middleware\HttpsProtocol;
use App\Http\Middleware\Locale;
use App\Http\Middleware\LocaleAdmin;
use App\Http\Middleware\PreventRequestsDuringMaintenance;
use App\Http\Middleware\SiteOpen;
use App\Http\Middleware\TrimStrings;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Auth\Middleware\RequirePassword;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
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
            PreventRequestsDuringMaintenance::class,
            ValidatePostSize::class,
            TrimStrings::class,
            ConvertEmptyStringsToNull::class,
            HttpsProtocol::class,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Middleware Groups
        |--------------------------------------------------------------------------
        */
        $middleware->group('web', [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
        ]);

        $middleware->group('api', [
            EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            SubstituteBindings::class,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Route Middleware Aliases
        |--------------------------------------------------------------------------
        */
        $middleware->alias([
            'auth' => Authenticate::class,
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
            'auth.basic' => AuthenticateWithBasicAuth::class,
            'cache.headers' => SetCacheHeaders::class,
            'can' => Authorize::class,
            'password.confirm' => RequirePassword::class,
            'signed' => ValidateSignature::class,
            'throttle' => ThrottleRequests::class,
            'verified' => EnsureEmailIsVerified::class,

            // Custom
            'access.all' => AccessAll::class,
            'check.lastactive' => CheckLastActive::class,
            'check.lastactive.admin' => CheckLastActiveAdmin::class,
            'locale' => Locale::class,
            'locale.admin' => LocaleAdmin::class,
            'site.open' => SiteOpen::class,
            'auto-check-permission' => AutoCheckPermission::class,
            'cors' => Cors::class,
            'json.response' => ForceJsonResponse::class,
            'check.socialLogin' => CheckSocialLoginAccess::class,
            'check.client' => EnsureUserIsClient::class,
            'admin' => EnsureUserIsAdmin::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {

        // API only: every error is answered as JSON, whatever the request asked for.
        $exceptions->shouldRenderJsonWhen(fn (Request $request) => true);

        $exceptions->dontReport([
            NotFoundHttpException::class,
            TokenMismatchException::class,
        ]);

        $json = function (string $status, string $message, int $code, array $extra = []) {
            return response()->json(['status' => $status, 'message' => $message] + $extra, $code);
        };

        $exceptions->render(function (ValidationException $e, Request $request) use ($json) {
            return $json('error', __('validation_error'), 422, ['errors' => $e->errors()]);
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) use ($json) {
            return $json('unauthenticated', __('Please login'), 401);
        });

        $exceptions->render(function (AuthorizationException $e, Request $request) use ($json) {
            return $json('unauthorized', __("You don't have permission to do this"), 403);
        });

        $exceptions->render(function (ModelNotFoundException|NotFoundHttpException $e, Request $request) use ($json) {
            return $json('error', __('Not found'), 404);
        });

        $exceptions->render(function (Throwable $e, Request $request) use ($json) {
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

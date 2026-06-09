<?php

use App\Http\Middleware\ApiMiddleware;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\EncryptCookies;
use App\Http\Middleware\PreventRequestsDuringMaintenance;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\TrimStrings;
use App\Http\Middleware\TrustProxies;
use App\Http\Middleware\ValidateSignature;
use App\Http\Middleware\VerifyCsrfToken;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiValidationResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Auth\Middleware\RequirePassword;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Validation\ValidationException;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Modules\Auth\App\Http\Middleware\EnsureJwtScopeMiddleware;
use Modules\I18N\App\Http\Middlewares\I18NApiMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__."/../routes/console.php",
        channels: __DIR__."/../routes/channels.php",
        health: "/up",
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->use([
            TrustProxies::class,
            HandleCors::class,
            PreventRequestsDuringMaintenance::class,
            ValidatePostSize::class,
            TrimStrings::class,
            ConvertEmptyStringsToNull::class,
        ]);

        $middleware->web(append: [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
        ]);

        $middleware->api(prepend: [
            SubstituteBindings::class,
            ApiMiddleware::class,
            I18NApiMiddleware::class,
        ]);

        $middleware->alias([
            "auth" => Authenticate::class,
            "auth.basic" => AuthenticateWithBasicAuth::class,
            "auth.session" => AuthenticateSession::class,
            "cache.headers" => SetCacheHeaders::class,
            "can" => Authorize::class,
            "guest" => RedirectIfAuthenticated::class,
            "password.confirm" => RequirePassword::class,
            "precognitive" => HandlePrecognitiveRequests::class,
            "signed" => ValidateSignature::class,
            "throttle" => ThrottleRequests::class,
            "verified" => EnsureEmailIsVerified::class,
            "jwt.scope" => EnsureJwtScopeMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is("api/*") || $request->expectsJson(),
        );

        $exceptions->render(function (ValidationException $exception, Request $request) {
            if ($request->is("api/*") || $request->expectsJson()) {
                return ApiValidationResponse::fromException($exception, $request);
            }
        });

        $exceptions->render(function (AuthenticationException $exception, Request $request) {
            if ($request->is("api/*") || $request->expectsJson()) {
                return ApiErrorResponse::message(__("auth.not_authorized"), 401);
            }
        });
    })
    ->create();

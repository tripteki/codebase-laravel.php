<?php

namespace App\Http;

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\EncryptCookies;
use App\Http\Middleware\EnsureCentralAdmin;
use App\Http\Middleware\HandleInertiaRequestsResponses;
use App\Http\Middleware\PreventRequestsDuringMaintenance;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\TrimStrings;
use App\Http\Middleware\TrustProxies;
use App\Http\Middleware\ValidateSignature;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // TrustHosts::class,
        TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        "web" => [
            EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            HandleInertiaRequestsResponses::class,
        ],

        "api" => [
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Src\V1\Api\Common\Http\Middlewares\ApiMiddleware::class,
            \Src\V1\Api\I18N\Http\Middlewares\I18NApiMiddleware::class,
        ],
    ];

    /**
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
        "auth" => Authenticate::class,
        "auth.basic" => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        "auth.session" => \Illuminate\Session\Middleware\AuthenticateSession::class,
        "cache.headers" => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        "can" => \Illuminate\Auth\Middleware\Authorize::class,
        "guest" => RedirectIfAuthenticated::class,
        "password.confirm" => \Illuminate\Auth\Middleware\RequirePassword::class,
        "precognitive" => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        "signed" => ValidateSignature::class,
        "throttle" => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        "verified" => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        "i18n" => \Src\V1\Api\I18N\Http\Middlewares\I18NWebMiddleware::class,
        "central.admin" => EnsureCentralAdmin::class,
    ];
}

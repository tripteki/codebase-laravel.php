<?php

namespace App\Http;

use Tripteki\ACL\Http\Middleware\RoleOrPermissionMiddleware;
use Tripteki\ACL\Http\Middleware\PermissionMiddleware;
use Tripteki\ACL\Http\Middleware\RoleMiddleware;
use Tripteki\SettingLocale\Http\Middleware\TranslationMiddleware;
use App\Http\Middleware\Api as ApiMiddleware;
use App\Http\Middleware\Authenticate as AuthenticateMiddleware;
use App\Http\Middleware\EncryptCookies as EncryptCookiesMiddleware;
use App\Http\Middleware\PreventRequestsDuringMaintenance as PreventRequestsResponsesDuringMaintenanceMiddleware;
use App\Http\Middleware\RedirectIfAuthenticated as RedirectIfAuthenticatedMiddleware;
use App\Http\Middleware\TrimStrings as TrimStringsMiddleware;
use App\Http\Middleware\TrustHosts as TrustHostsMiddleware;
use App\Http\Middleware\TrustProxies as TrustProxiesMiddleware;
use App\Http\Middleware\ValidateSignature as ValidateSignatureMiddleware;
use App\Http\Middleware\VerifyCsrfToken as VerifyCsrfTokensMiddleware;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth as AuthenticateBasicAuthMiddleware;
use Illuminate\Auth\Middleware\Authorize as AuthorizeMiddleware;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified as EnsureEmailIsVerifiedMiddleware;
use Illuminate\Auth\Middleware\RequirePassword as RequirePasswordMiddleware;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse as AddQueuedCookiesToRequestsResponsesMiddleware;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull as ConvertEmptyStringsToNullMiddleware;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize as ValidatePostsSizeMiddleware;
use Illuminate\Http\Middleware\HandleCors as HandleCorsMiddleware;
use Illuminate\Http\Middleware\SetCacheHeaders as SetCacheHeadersMiddleware;
use Illuminate\Routing\Middleware\SubstituteBindings as SubstituteBindingsMiddleware;
use Illuminate\Routing\Middleware\ThrottleRequests as ThrottleRequestsResponsesMiddleware;
use Illuminate\Session\Middleware\AuthenticateSession as AuthenticateSessionMiddleware;
use Illuminate\Session\Middleware\StartSession as StartSessionsMiddleware;
use Illuminate\View\Middleware\ShareErrorsFromSession as ShareErrorsFromSessionMiddleware;

class Kernel extends HttpKernel
{
    /**
     * @var array<int, class-string|string>
     */
    protected $middleware = [

        TrustHostsMiddleware::class,
        TrustProxiesMiddleware::class,
        HandleCorsMiddleware::class,
        PreventRequestsResponsesDuringMaintenanceMiddleware::class,
        ValidatePostsSizeMiddleware::class,
        TrimStringsMiddleware::class,
        ConvertEmptyStringsToNullMiddleware::class,
    ];

    /**
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [

        "web" => [

            EncryptCookiesMiddleware::class,
            AddQueuedCookiesToRequestsResponsesMiddleware::class,
            StartSessionsMiddleware::class,
            ShareErrorsFromSessionMiddleware::class,
            VerifyCsrfTokensMiddleware::class,
            SubstituteBindingsMiddleware::class,
        ],

        "api" => [

            ApiMiddleware::class,

            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            "throttle:api",
            SubstituteBindingsMiddleware::class,
        ],
    ];

    /**
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [

        "guest" => RedirectIfAuthenticatedMiddleware::class,

        "auth" => AuthenticateMiddleware::class,
        "auth.basic" => AuthenticateBasicAuthMiddleware::class,
        "auth.session" => AuthenticateSessionMiddleware::class,
        "can" => AuthorizeMiddleware::class,

        "password.confirm" => RequirePasswordMiddleware::class,
        "signed" => ValidateSignatureMiddleware::class,
        "verified" => EnsureEmailIsVerifiedMiddleware::class,
        "throttle" => ThrottleRequestsResponsesMiddleware::class,
        "cache.headers" => SetCacheHeadersMiddleware::class,
        "locale" => TranslationMiddleware::class,
        "role" => RoleMiddleware::class,
        "permission" => PermissionMiddleware::class,
        "role_or_permission" => RoleOrPermissionMiddleware::class,
    ];
}

<?php

namespace Modules\Auth\App\Providers;

use App\Http\Responses\ApiErrorResponse;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Providers\LaravelServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        parent::register();

        $this->app->register(RouteServiceProvider::class);
        $this->extendAuthGuard();
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        parent::boot();

        $this->loadViewsFrom(module_path("Auth", "resources/views"), "auth");
        $this->loadMigrationsFrom(module_path("Auth", "Database/migrations"));
        $this->registerTranslations();
        $this->registerVerificationUrls();
        $this->registerExceptions();
    }

    /**
     * @return void
     */
    protected function registerTranslations(): void
    {
        $langPath = module_path("Auth", "lang");

        if (! is_dir($langPath)) {
            return;
        }

        $translator = $this->app["translator"];

        foreach (glob($langPath."/*", GLOB_ONLYDIR) ?: [] as $localePath) {
            $locale = basename($localePath);

            foreach (glob($localePath."/*.php") ?: [] as $file) {
                $group = basename($file, ".php");
                $lines = require $file;
                $prefixed = [];

                foreach ($lines as $key => $value) {
                    $prefixed["{$group}.{$key}"] = $value;
                }

                $translator->addLines($prefixed, $locale);
            }
        }
    }

    /**
     * @return void
     */
    protected function registerVerificationUrls(): void
    {
        VerifyEmail::createUrlUsing(
            fn ($notifiable): string => signed_auth_frontend_url(
                $notifiable->tenant_id !== null ? (string) $notifiable->tenant_id : null,
                auth_verify_email_path($notifiable->getEmailForVerification()),
            ),
        );

        ResetPassword::createUrlUsing(
            fn ($notifiable, $token): string => signed_auth_frontend_url(
                $notifiable->tenant_id !== null ? (string) $notifiable->tenant_id : null,
                auth_reset_password_path($notifiable->getEmailForPasswordReset()),
            ),
        );
    }

    /**
     * @return void
     */
    protected function registerExceptions(): void
    {
        $exceptionHandler = resolve(ExceptionHandler::class);

        $exceptionHandler->renderable(function (\Tymon\JWTAuth\Exceptions\TokenInvalidException $exception, Request $request): JsonResponse {
            if ($request->wantsJson() || $request->is("api/*")) {
                return ApiErrorResponse::message(__("auth.token_invalid"), 401);
            }
        });

        $exceptionHandler->renderable(function (\Tymon\JWTAuth\Exceptions\TokenExpiredException $exception, Request $request): JsonResponse {
            if ($request->wantsJson() || $request->is("api/*")) {
                return ApiErrorResponse::message(__("auth.token_expired"), 401);
            }
        });

        $exceptionHandler->renderable(function (\Tymon\JWTAuth\Exceptions\TokenBlacklistedException $exception, Request $request): JsonResponse {
            if ($request->wantsJson() || $request->is("api/*")) {
                return ApiErrorResponse::message(__("auth.token_blacklisted"), 401);
            }
        });

        $exceptionHandler->renderable(function (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $exception, Request $request): JsonResponse {
            if ($request->wantsJson() || $request->is("api/*")) {
                return ApiErrorResponse::message(__("auth.user_not_defined"), 401);
            }
        });

        $exceptionHandler->renderable(function (\Tymon\JWTAuth\Exceptions\InvalidClaimException $exception, Request $request): JsonResponse {
            if ($request->wantsJson() || $request->is("api/*")) {
                return ApiErrorResponse::message(__("auth.invalid_claim"), 401);
            }
        });

        $exceptionHandler->renderable(function (\Tymon\JWTAuth\Exceptions\PayloadException $exception, Request $request): JsonResponse {
            if ($request->wantsJson() || $request->is("api/*")) {
                return ApiErrorResponse::message(__("auth.payload_invalid"), 401);
            }
        });
    }
}

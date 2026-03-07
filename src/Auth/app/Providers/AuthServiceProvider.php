<?php

namespace Modules\Auth\App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Debug\ExceptionHandler;
use App\Http\Responses\ApiErrorResponse;
use Illuminate\Support\Facades\Event;
use Modules\Auth\App\Listeners\SendEmailVerificationNotification;
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
        $this->registerEvents();
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
    protected function registerEvents(): void
    {
        Event::listen(Registered::class, SendEmailVerificationNotification::class);

        VerifyEmail::createUrlUsing(function ($notifiable): string {
            return signed_frontend_url("auth/verify-email/".$notifiable->getEmailForVerification());
        });

        ResetPassword::createUrlUsing(function ($notifiable, $token): string {
            return signed_frontend_url("auth/reset-password/".$notifiable->getEmailForPasswordReset());
        });
    }

    /**
     * @return void
     */
    protected function registerExceptions(): void
    {
        $exceptionHandler = resolve(ExceptionHandler::class);

        $exceptionHandler->renderable(function (\Tymon\JWTAuth\Exceptions\TokenInvalidException $exception, Request $request): JsonResponse {
            if ($request->wantsJson() || $request->is("api/*")) {
                return ApiErrorResponse::detail(__("auth.token_invalid"), 401);
            }
        });

        $exceptionHandler->renderable(function (\Tymon\JWTAuth\Exceptions\TokenExpiredException $exception, Request $request): JsonResponse {
            if ($request->wantsJson() || $request->is("api/*")) {
                return ApiErrorResponse::detail(__("auth.token_expired"), 401);
            }
        });

        $exceptionHandler->renderable(function (\Tymon\JWTAuth\Exceptions\TokenBlacklistedException $exception, Request $request): JsonResponse {
            if ($request->wantsJson() || $request->is("api/*")) {
                return ApiErrorResponse::detail(__("auth.token_blacklisted"), 401);
            }
        });

        $exceptionHandler->renderable(function (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $exception, Request $request): JsonResponse {
            if ($request->wantsJson() || $request->is("api/*")) {
                return ApiErrorResponse::detail(__("auth.user_not_defined"), 401);
            }
        });

        $exceptionHandler->renderable(function (\Tymon\JWTAuth\Exceptions\InvalidClaimException $exception, Request $request): JsonResponse {
            if ($request->wantsJson() || $request->is("api/*")) {
                return ApiErrorResponse::detail(__("auth.invalid_claim"), 401);
            }
        });

        $exceptionHandler->renderable(function (\Tymon\JWTAuth\Exceptions\PayloadException $exception, Request $request): JsonResponse {
            if ($request->wantsJson() || $request->is("api/*")) {
                return ApiErrorResponse::detail(__("auth.payload_invalid"), 401);
            }
        });
    }
}

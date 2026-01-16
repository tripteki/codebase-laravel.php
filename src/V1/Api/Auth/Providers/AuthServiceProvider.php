<?php

namespace Src\V1\Api\Auth\Providers;

use Throwable;
use Exception;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\Notifications\ResetPassword;
use Tymon\JWTAuth\Providers\LumenServiceProvider;
use Tymon\JWTAuth\Providers\LaravelServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        parent::register();

        $this->extendAuthGuard();
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        parent::boot();

        $this->registerExceptions();
        $this->registerRoutes();
    }

    /**
     * @return void
     */
    protected function registerExceptions(): void
    {
        $exceptionHandler = resolve(ExceptionHandler::class);

        $exceptionHandler->renderable(function (\Tymon\JWTAuth\Exceptions\TokenInvalidException $exception, Request $request): JsonResponse {
            if ($request->wantsJson() || $request->is("api/*")) {
                return response()->json([
                    "message" => __("auth.token_invalid"),
                ], 401);
            }
        });

        $exceptionHandler->renderable(function (\Tymon\JWTAuth\Exceptions\TokenExpiredException $exception, Request $request): JsonResponse {
            if ($request->wantsJson() || $request->is("api/*")) {
                return response()->json([
                    "message" => __("auth.token_expired"),
                ], 401);
            }
        });

        $exceptionHandler->renderable(function (\Tymon\JWTAuth\Exceptions\TokenBlacklistedException $exception, Request $request): JsonResponse {
            if ($request->wantsJson() || $request->is("api/*")) {
                return response()->json([
                    "message" => __("auth.token_blacklisted"),
                ], 401);
            }
        });

        $exceptionHandler->renderable(function (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $exception, Request $request): JsonResponse {
            if ($request->wantsJson() || $request->is("api/*")) {
                return response()->json([
                    "message" => __("auth.user_not_defined"),
                ], 401);
            }
        });

        $exceptionHandler->renderable(function (\Tymon\JWTAuth\Exceptions\InvalidClaimException $exception, Request $request): JsonResponse {
            if ($request->wantsJson() || $request->is("api/*")) {
                return response()->json([
                    "message" => __("auth.invalid_claim"),
                ], 401);
            }
        });

        $exceptionHandler->renderable(function (\Tymon\JWTAuth\Exceptions\PayloadException $exception, Request $request): JsonResponse {
            if ($request->wantsJson() || $request->is("api/*")) {
                return response()->json([
                    "message" => __("auth.payload_invalid"),
                ], 401);
            }
        });
    }

    /**
     * @return void
     */
    protected function registerRoutes(): void
    {
        ResetPassword::createUrlUsing(function ($notifiable, $token): string {

            return url(route("admin.password.reset", [
                "token" => $token,
                "email" => $notifiable->getEmailForPasswordReset(),
            ], false));
        });
    }
}

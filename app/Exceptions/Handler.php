<?php

namespace App\Exceptions;

use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiValidationResponse;
use App\Models\User;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Notifications\DatabaseNotification;
use Modules\Acl\App\Models\Permission;
use Modules\Acl\App\Models\Role;
use Modules\Log\App\Models\Activity;
use Modules\Notification\App\Models\Notification;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * @var array<int, string>
     */
    protected $dontFlash = [
        "current_password",
        "password",
        "password_confirmation",
    ];

    /**
     * @param class-string|null $model
     * @return string
     */
    protected static function missingModelMessage(?string $model): string
    {
        return match ($model) {
            User::class => __("user.auth.user_not_found"),
            Role::class => __("acl.role.not_found"),
            Permission::class => __("acl.permission.not_found"),
            Activity::class => __("log.activity.not_found"),
            Notification::class, DatabaseNotification::class => __("notification.not_found"),
            default => __("route.model_not_found"),
        };
    }

    /**
     * @return void
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $exception, \Illuminate\Http\Request $request) {
            if ($request->wantsJson() || $request->is("api/*")) {
                $previous = $exception->getPrevious();
                $message = $previous instanceof \Illuminate\Database\Eloquent\ModelNotFoundException
                    ? self::missingModelMessage($previous->getModel())
                    : __("route.not_found");

                return ApiErrorResponse::message($message, 404);
            }
        });

        $this->renderable(function (\Illuminate\Database\Eloquent\ModelNotFoundException $exception, \Illuminate\Http\Request $request) {
            if ($request->wantsJson() || $request->is("api/*")) {
                return ApiErrorResponse::message(self::missingModelMessage($exception->getModel()), 404);
            }
        });

        $this->renderable(function (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $exception, \Illuminate\Http\Request $request) {
            if ($request->wantsJson() || $request->is("api/*")) {
                return ApiErrorResponse::message("Method Not Allowed", 405)->withHeaders($exception->getHeaders());
            }
        });

        $this->renderable(function (\Illuminate\Auth\Access\AuthorizationException $exception, \Illuminate\Http\Request $request) {
            if ($request->wantsJson() || $request->is("api/*")) {
                return ApiErrorResponse::message(__("auth.forbidden"), 403);
            }
        });

        $this->renderable(function (\Symfony\Component\HttpKernel\Exception\HttpException $exception, \Illuminate\Http\Request $request) {
            if ($request->wantsJson() || $request->is("api/*")) {
                $message = $exception->getMessage();

                if ($exception->getStatusCode() === 403 && $message === "This action is unauthorized.") {
                    $message = __("auth.forbidden");
                }

                if ($message === "") {
                    $message = match ($exception->getStatusCode()) {
                        401 => __("auth.not_authorized"),
                        403 => __("auth.not_authorized"),
                        404 => __("route.not_found"),
                        default => "An error occurred.",
                    };
                }

                return ApiErrorResponse::message($message, $exception->getStatusCode());
            }
        });

        $this->renderable(function (\Illuminate\Auth\AuthenticationException $exception, \Illuminate\Http\Request $request) {
            if ($request->wantsJson() || $request->is("api/*")) {
                return ApiErrorResponse::message(__("auth.not_authorized"), 401);
            }
        });

        $this->renderable(function (\Illuminate\Validation\ValidationException $exception, \Illuminate\Http\Request $request) {
            if ($request->wantsJson() || $request->is("api/*")) {
                return ApiValidationResponse::fromException($exception, $request);
            }
        });

        $this->renderable(function (\Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException $exception, \Illuminate\Http\Request $request) {
            if ($request->wantsJson() || $request->is("api/*")) {
                $message = $exception->getMessage();

                if ($message === "") {
                    $message = __("auth.throttle", [ "seconds" => $exception->getHeaders()["Retry-After"] ?? 60, ]);
                }

                return ApiErrorResponse::message($message, 429);
            }
        });
    }
}

<?php

namespace App\Exceptions;

use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiValidationResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
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
     * @return void
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $exception, \Illuminate\Http\Request $request) {
            if ($request->wantsJson() || $request->is("api/*")) {
                return ApiErrorResponse::message(__("route.not_found"), 404);
            }
        });

        $this->renderable(function (\Illuminate\Database\Eloquent\ModelNotFoundException $exception, \Illuminate\Http\Request $request) {
            if ($request->wantsJson() || $request->is("api/*")) {
                return ApiErrorResponse::message(__("route.model_not_found"), 404);
            }
        });

        $this->renderable(function (\Illuminate\Auth\Access\AuthorizationException $exception, \Illuminate\Http\Request $request) {
            if ($request->wantsJson() || $request->is("api/*")) {
                return ApiErrorResponse::message(__("auth.not_authorized"), 403);
            }
        });

        $this->renderable(function (\Symfony\Component\HttpKernel\Exception\HttpException $exception, \Illuminate\Http\Request $request) {
            if ($request->wantsJson() || $request->is("api/*")) {
                $message = $exception->getMessage();

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

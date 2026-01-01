<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        "current_password",
        "password",
        "password_confirmation",
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $exception, \Illuminate\Http\Request $request) {
            if ($request->wantsJson() || $request->is("api/*")) {
                return response()->json([
                    "message" => __("route.not_found"),
                ], 400);
            }
        });

        $this->renderable(function (\Illuminate\Database\Eloquent\ModelNotFoundException $exception, \Illuminate\Http\Request $request) {
            if ($request->wantsJson() || $request->is("api/*")) {
                return response()->json([
                    "message" => __("route.model_not_found"),
                ], 404);
            }
        });

        $this->renderable(function (\Illuminate\Auth\AuthenticationException $exception, \Illuminate\Http\Request $request) {
            if ($request->wantsJson() || $request->is("api/*")) {
                return response()->json([
                    "message" => __("route.unauthenticated"),
                ], 401);
            }
        });

        $this->renderable(function (\Illuminate\Validation\ValidationException $exception, \Illuminate\Http\Request $request) {
            if ($request->wantsJson() || $request->is("api/*")) {
                return response()->json([
                    "message" => __("route.unprocessable_entity"),
                    "errors" => $exception->errors(),
                ], 422);
            }
        });
    }
}

<?php

namespace App\Providers;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerExceptions();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Register exceptions.
     */
    protected function registerExceptions(): void
    {
        $exceptionHandler = resolve(ExceptionHandler::class);

        $exceptionHandler->renderable(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $exception, Request $request) {
            if ($request->wantsJson() || $request->is("api/*")) {
                return response()->json([
                    "message" => __("route.not_found"),
                ], 400);
            }
        });

        $exceptionHandler->renderable(function (\Illuminate\Database\Eloquent\ModelNotFoundException $exception, Request $request) {
            if ($request->wantsJson() || $request->is("api/*")) {
                return response()->json([
                    "message" => __("route.model_not_found"),
                ], 404);
            }
        });

        $exceptionHandler->renderable(function (\Illuminate\Auth\AuthenticationException $exception, Request $request) {
            if ($request->wantsJson() || $request->is("api/*")) {
                return response()->json([
                    "message" => __("route.unauthenticated"),
                ], 401);
            }
        });

        $exceptionHandler->renderable(function (\Illuminate\Validation\ValidationException $exception, Request $request) {
            if ($request->wantsJson() || $request->is("api/*")) {
                return response()->json([
                    "message" => __("route.unprocessable_entity"),
                    "errors" => $exception->errors(),
                ], 422);
            }
        });
    }
}

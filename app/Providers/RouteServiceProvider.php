<?php

namespace App\Providers;

use App\Http\Controllers\AdminSearchController;
use App\Support\Throttle;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    public const HOME = "/api/docs";

    /**
     * @return void
     */
    public function boot(): void
    {
        RateLimiter::for("api", fn (Request $request) => Limit::perMinute(60)->by($request->user()?->id ?: $request->ip()));
        RateLimiter::for("api-read", fn (Request $request) => Limit::perMinute(10)->by($request->user()?->id ?: $request->ip()));
        RateLimiter::for("api-write", fn (Request $request) => Limit::perMinute(30)->by($request->user()?->id ?: $request->ip()));

        RateLimiter::for("api-register", fn (Request $request) => Limit::perMinute(5)->by($request->ip()));
        RateLimiter::for("api-refresh", fn (Request $request) => Limit::perMinute(30)->by($request->user()?->id ?: $request->ip()));

        RateLimiter::for("app-version", fn (Request $request) => Limit::perMinute(60)->by($request->ip()));
        RateLimiter::for("app-status", fn (Request $request) => Limit::perMinute(120)->by($request->ip()));

        $this->routes(function () {
            Route::middleware("api")
                ->prefix("api")
                ->group(base_path("routes/api.php"));

            Route::middleware("web")
                ->group(base_path("routes/web.php"));

            $searchMiddleware = [
                "auth:api",
                "jwt.scope:ACCESS_TOKEN",
                "verified",
                ...Throttle::middleware("api-read"),
            ];

            Route::middleware("api")->group(function () use ($searchMiddleware): void {
                Route::middleware([...$searchMiddleware, "central.admin"])
                    ->prefix("api/v1/admin")
                    ->get("search", [AdminSearchController::class, "index"]);

                Route::middleware([...$searchMiddleware, "tenant.api"])
                    ->prefix("api/v1/{tenant}/admin")
                    ->get("search", [AdminSearchController::class, "index"]);
            });
        });
    }
}

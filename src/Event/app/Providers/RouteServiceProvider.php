<?php

namespace Modules\Event\App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Modules\Event\App\Http\Controllers\AddOnVariablesController;

class RouteServiceProvider extends ServiceProvider
{
    protected string $moduleNamespace = "Modules\\Event\\App\\Http\\Controllers";

    /**
     * @return void
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * @return void
     */
    public function map(): void
    {
        Route::prefix("api")
            ->middleware([
                "api",
                "auth:api",
                "jwt.scope:ACCESS_TOKEN",
                "verified",
                "central.admin",
            ])
            ->namespace($this->moduleNamespace)
            ->group(function (): void {
                Route::prefix("v1/admin")->group(module_path("Event", "routes/admin.php"));
            });

        Route::prefix("api")
            ->middleware(["api", "tenant.api"])
            ->namespace($this->moduleNamespace)
            ->group(function (): void {
                Route::get("v1/{tenant}/add-ons/variables", [AddOnVariablesController::class, "index"]);
            });
    }
}

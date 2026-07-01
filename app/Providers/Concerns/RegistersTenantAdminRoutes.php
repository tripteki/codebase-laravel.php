<?php

namespace App\Providers\Concerns;

use Illuminate\Support\Facades\Route;

trait RegistersTenantAdminRoutes
{
    /**
     * @param string $module
     * @param string $controllerNamespace
     * @return void
     */
    protected function registerTenantAwareAdminRoutes(string $module, string $controllerNamespace): void
    {
        $routesFile = module_path($module, "routes/admin.php");
        $middleware = ["auth:api", "jwt.scope:ACCESS_TOKEN", "verified"];

        Route::prefix("api")
            ->middleware("api")
            ->namespace($controllerNamespace)
            ->group(function () use ($middleware, $routesFile): void {
                Route::middleware([...$middleware, "central.admin"])
                    ->prefix("v1/admin")
                    ->group($routesFile);

                Route::middleware([...$middleware, "tenant.api"])
                    ->prefix("v1/{tenant}/admin")
                    ->group($routesFile);
            });
    }
}

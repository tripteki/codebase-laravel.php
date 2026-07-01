<?php

namespace Modules\User\App\Providers;

use App\Providers\Concerns\RegistersTenantAdminRoutes;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Modules\User\App\Http\Controllers\SettingVariablesController;

class RouteServiceProvider extends ServiceProvider
{
    use RegistersTenantAdminRoutes;

    protected string $moduleNamespace = "Modules\\User\\App\\Http\\Controllers";

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
        $this->mapApiRoutes();
    }

    /**
     * @return void
     */
    protected function mapApiRoutes(): void
    {
        Route::prefix("api")
            ->middleware("api")
            ->namespace($this->moduleNamespace)
            ->group(module_path("User", "routes/api.php"));

        Route::prefix("api")
            ->middleware(["api", "tenant.api"])
            ->namespace($this->moduleNamespace)
            ->group(function (): void {
                Route::get("v1/{tenant}/settings/variables", [SettingVariablesController::class, "index"]);
            });

        Route::prefix("api")
            ->middleware("api")
            ->namespace($this->moduleNamespace)
            ->group(module_path("User", "routes/admin-central.php"));

        $this->registerTenantAwareAdminRoutes("User", $this->moduleNamespace);
    }
}

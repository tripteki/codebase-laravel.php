<?php

namespace Modules\Notification\App\Providers;

use App\Providers\Concerns\RegistersTenantAdminRoutes;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    use RegistersTenantAdminRoutes;

    protected string $moduleNamespace = "Modules\\Notification\\App\\Http\\Controllers";

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
            ->group(module_path("Notification", "routes/api.php"));

        $this->registerTenantAwareAdminRoutes("Notification", $this->moduleNamespace);
    }
}

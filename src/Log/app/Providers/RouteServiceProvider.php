<?php

namespace Modules\Log\App\Providers;

use App\Providers\Concerns\RegistersTenantAdminRoutes;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    use RegistersTenantAdminRoutes;

    protected string $moduleNamespace = "Modules\\Log\\App\\Http\\Controllers";

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
        $this->registerTenantAwareAdminRoutes("Log", $this->moduleNamespace);
    }
}

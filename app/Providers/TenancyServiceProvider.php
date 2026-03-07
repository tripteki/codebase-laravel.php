<?php

namespace App\Providers;

use Stancl\JobPipeline\JobPipeline;
use Stancl\Tenancy\Events;
use Stancl\Tenancy\Listeners;
use Stancl\Tenancy\Jobs;
use Stancl\Tenancy\Middleware;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class TenancyServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    public static string $controllerNamespace = "";

    /**
     * @return array<string, array<int, mixed>>
     */
    public function events()
    {
        return [

            // Tenant events-listeners
            Events\CreatingTenant::class => [

                //
            ],
            Events\TenantCreated::class => [

                //
            ],
            Events\SavingTenant::class => [],
            Events\TenantSaved::class => [],
            Events\UpdatingTenant::class => [],
            Events\TenantUpdated::class => [],
            Events\DeletingTenant::class => [],
            Events\TenantDeleted::class => [

                //
            ],

            // Domain events-listeners
            Events\CreatingDomain::class => [],
            Events\DomainCreated::class => [],
            Events\SavingDomain::class => [],
            Events\DomainSaved::class => [],
            Events\UpdatingDomain::class => [],
            Events\DomainUpdated::class => [],
            Events\DeletingDomain::class => [],
            Events\DomainDeleted::class => [],

            // Database events-listeners
            Events\DatabaseCreated::class => [],
            Events\DatabaseMigrated::class => [],
            Events\DatabaseSeeded::class => [],
            Events\DatabaseRolledBack::class => [],
            Events\DatabaseDeleted::class => [],

            // Tenancy events
            Events\InitializingTenancy::class => [],
            Events\TenancyInitialized::class => [

                Listeners\BootstrapTenancy::class,
            ],
            Events\EndingTenancy::class => [],
            Events\TenancyEnded::class => [

                Listeners\RevertToCentralContext::class,
            ],
            Events\BootstrappingTenancy::class => [],
            Events\TenancyBootstrapped::class => [],
            Events\RevertingToCentralContext::class => [],
            Events\RevertedToCentralContext::class => [],

            // Resource syncing
            Events\SyncedResourceSaved::class => [

                Listeners\UpdateSyncedResource::class,
            ],

            Events\SyncedResourceChangedInForeignDatabase::class => [],
        ];
    }

    /**
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        $this->bootEvents();
        $this->mapRoutes();

        $this->makeTenancyMiddlewareHighestPriority();
    }

    /**
     * @return void
     */
    protected function bootEvents(): void
    {
        foreach ($this->events() as $event => $listeners) {

            foreach ($listeners as $listener) {

                if ($listener instanceof JobPipeline) {

                    $listener = $listener->toListener();
                }

                Event::listen($event, $listener);
            }
        }
    }

    /**
     * @return void
     */
    protected function mapRoutes(): void
    {
        $this->app->booted(function () {

            if (file_exists(base_path("routes/tenant/web.php"))) {

                Route::namespace(static::$controllerNamespace)->group(base_path("routes/tenant/web.php"));
            }
        });
    }

    /**
     * @return void
     */
    protected function makeTenancyMiddlewareHighestPriority(): void
    {
        $tenancyMiddleware = [

            Middleware\PreventAccessFromCentralDomains::class,
            Middleware\InitializeTenancyByDomain::class,
            Middleware\InitializeTenancyBySubdomain::class,
            Middleware\InitializeTenancyByDomainOrSubdomain::class,
            Middleware\InitializeTenancyByPath::class,
            Middleware\InitializeTenancyByRequestData::class,
        ];

        foreach (array_reverse($tenancyMiddleware) as $middleware) {

            $this->app[\Illuminate\Contracts\Http\Kernel::class]->prependToMiddlewarePriority($middleware);
        }
    }
}

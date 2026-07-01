<?php

namespace App\Providers;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Event as EventFacade;
use Illuminate\Support\ServiceProvider;
use Stancl\Tenancy\Events;
use Stancl\Tenancy\Listeners;
use Stancl\Tenancy\Middleware;

class TenancyServiceProvider extends ServiceProvider
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function events(): array
    {
        return [
            Events\TenancyInitialized::class => [
                Listeners\BootstrapTenancy::class,
            ],
            Events\TenancyEnded::class => [
                Listeners\RevertToCentralContext::class,
            ],
        ];
    }

    /**
     * @return void
     */
    public function register(): void {}

    /**
     * @return void
     */
    public function boot(): void
    {
        $this->bootEvents();
        $this->makeTenancyMiddlewareHighestPriority();
    }

    /**
     * @return void
     */
    protected function bootEvents(): void
    {
        foreach ($this->events() as $event => $listeners) {
            foreach ($listeners as $listener) {
                EventFacade::listen($event, $listener);
            }
        }

        EventFacade::listen(Events\TenancyInitialized::class, function (): void {
            sync_permissions_team_context();
        });

        EventFacade::listen(Events\TenancyEnded::class, function (): void {
            sync_permissions_team_context();
        });
    }

    /**
     * @return void
     */
    protected function makeTenancyMiddlewareHighestPriority(): void
    {
        if (! $this->app->bound(Kernel::class)) {
            return;
        }

        $tenancyMiddleware = [
            Middleware\PreventAccessFromCentralDomains::class,
            Middleware\InitializeTenancyByDomain::class,
            Middleware\InitializeTenancyBySubdomain::class,
            Middleware\InitializeTenancyByDomainOrSubdomain::class,
            Middleware\InitializeTenancyByPath::class,
            Middleware\InitializeTenancyByRequestData::class,
        ];

        foreach (array_reverse($tenancyMiddleware) as $middleware) {
            $this->app[Kernel::class]->prependToMiddlewarePriority($middleware);
        }
    }
}

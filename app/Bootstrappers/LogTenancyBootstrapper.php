<?php

namespace App\Bootstrappers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Contracts\Tenant as TenantContract;
use Stancl\Tenancy\Contracts\TenancyBootstrapper;

class LogTenancyBootstrapper implements TenancyBootstrapper
{
    /**
     * @var array<string, mixed>
     */
    private array $previousContext = [];

    /**
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @return void
     */
    public function __construct(
        private readonly Application $app
    ) {}

    /**
     * @param \Stancl\Tenancy\Contracts\Tenant $tenant
     * @return void
     */
    public function bootstrap(TenantContract $tenant): void
    {
        $this->forgetLogChannels();

        $logsDir = $this->app->storagePath('logs');
        if (! is_dir($logsDir)) {
            @mkdir($logsDir, 0755, true);
        }

        $this->previousContext = Log::sharedContext();

        $context = [
            'tenant_id' => $tenant->getKey(),
        ];

        $title = $tenant->getAttribute('title');
        if (is_string($title) && $title !== '') {
            $context['tenant_title'] = $title;
        }

        $domain = $tenant->domains()->first()?->domain;
        if (is_string($domain) && $domain !== '') {
            $context['tenant_domain'] = $domain;
        }

        Log::shareContext($context);
    }

    /**
     * @return void
     */
    public function revert(): void
    {
        $this->forgetLogChannels();

        Log::flushSharedContext();
        if ($this->previousContext !== []) {
            Log::shareContext($this->previousContext);
        }
        $this->previousContext = [];
    }

    /**
     * @return void
     */
    private function forgetLogChannels(): void
    {
        $manager = $this->app->make('log');
        $channels = array_keys($this->app->make('config')->get('logging.channels', []));
        foreach ($channels as $name) {
            $manager->forgetChannel($name);
        }
    }
}

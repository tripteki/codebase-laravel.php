<?php

namespace Modules\Log\App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Log\App\Models\Activity;
use Modules\Log\App\Policies\ActivityPolicy;

class LogServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(module_path("Log", "Database/migrations"));

        Gate::policy(Activity::class, ActivityPolicy::class);
    }
}

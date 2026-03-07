<?php

namespace Modules\Notification\App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Notification\App\Models\Notification;
use Modules\Notification\App\Policies\NotificationPolicy;

class NotificationServiceProvider extends ServiceProvider
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
        $this->loadMigrationsFrom(module_path("Notification", "Database/migrations"));

        Gate::policy(Notification::class, NotificationPolicy::class);
    }
}

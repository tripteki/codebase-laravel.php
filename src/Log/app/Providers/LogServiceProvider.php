<?php

namespace Modules\Log\App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Log\App\Listeners\PermissionAssignmentActivityListener;
use Modules\Log\App\Models\Activity;
use Modules\Log\App\Policies\ActivityPolicy;
use Spatie\Permission\Events\PermissionAttached;
use Spatie\Permission\Events\PermissionDetached;
use Spatie\Permission\Events\RoleAttached;
use Spatie\Permission\Events\RoleDetached;

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

        $assignmentListener = PermissionAssignmentActivityListener::class;

        Event::listen(RoleAttached::class, [ $assignmentListener, "handleRoleAttached", ]);
        Event::listen(RoleDetached::class, [ $assignmentListener, "handleRoleDetached", ]);
        Event::listen(PermissionAttached::class, [ $assignmentListener, "handlePermissionAttached", ]);
        Event::listen(PermissionDetached::class, [ $assignmentListener, "handlePermissionDetached", ]);
    }
}

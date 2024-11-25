<?php

namespace Src\V1\Api\User\Providers;

use App\Models\User;
use Src\V1\Api\User\Policies\UserPolicy;
use Src\V1\Api\User\Events\UserActivatedEvent;
use Src\V1\Api\User\Events\UserDeactivatedEvent;
use Src\V1\Api\User\Listeners\UserAccountListener;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
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
        $this->registerPolicies();
        $this->registerEventListeners();
    }

    /**
     * @return void
     */
    protected function registerPolicies(): void
    {
        Gate::policy(
            User::class,
            UserPolicy::class
        );
    }

    /**
     * @return void
     */
    protected function registerEventListeners(): void
    {
        Event::listen(
            UserActivatedEvent::class,
            UserAccountListener::class
        );

        Event::listen(
            UserDeactivatedEvent::class,
            UserAccountListener::class
        );
    }
}

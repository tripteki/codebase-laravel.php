<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define("viewPulse", function (User $user) {

            return $user->hasRole(\Tripteki\ACL\Providers\ACLServiceProvider::$SUPERADMIN);
        });
    }
}

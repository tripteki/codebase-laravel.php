<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register()
    {
        \Tripteki\Docs\Providers\DocsServiceProvider::ignoreConfig();
        \Tripteki\Adminer\Providers\AdminerServiceProvider::ignoreConfig();
        \Tripteki\Log\Providers\LogServiceProvider::ignoreConfig();
        \Tripteki\ACL\Providers\ACLServiceProvider::ignoreConfig();
    }

    /**
     * @return void
     */
    public function boot()
    {
        User::observe(\Tripteki\Uid\Observers\UniqueIdObserver::class);
    }
}

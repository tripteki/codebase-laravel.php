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
    }

    /**
     * @return void
     */
    public function boot()
    {
        User::observe(\Tripteki\Uid\Observers\UniqueIdObserver::class);
    }
}

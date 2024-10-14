<?php

namespace Src\V1\Sample\Providers;

use Src\V1\Common\Providers\ServiceProvider as BaseServiceProvider;

class SampleBaseServiceProvider extends BaseServiceProvider
{
    /**
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->app->register(SampleAuthServiceProvider::class);
        $this->app->register(SampleRouteServiceProvider::class);
        $this->app->register(SampleEventListenerServiceProvider::class);

        $this->applyModuleName("Sample");
    }

    /**
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
};

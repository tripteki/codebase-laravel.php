<?php

namespace Src\V1\Sample\Providers;

use Src\V1\Common\Providers\RouteServiceProvider as BaseServiceProvider;

class SampleRouteServiceProvider extends BaseServiceProvider
{
    /**
     * @return void
     */
    public function register()
    {
        parent::register();

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

<?php

namespace Src\V1;

use Illuminate\Support\ServiceProvider;

class V1ServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register()
    {
        $this->app->register(\Src\V1\Sample\Providers\SampleBaseServiceProvider::class);
    }
};

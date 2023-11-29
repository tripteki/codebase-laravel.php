<?php

namespace App\Providers;

use Tymon\JWTAuth\Providers\LumenServiceProvider;
use Tymon\JWTAuth\Providers\LaravelServiceProvider as ServiceProvider;

class AuthTokenServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->extendAuthGuard();
    }
}

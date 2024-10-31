<?php

return [

    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,

    \Src\V0\Auth\Providers\AuthTokenServiceProvider::class,
    \Src\V1\Sample\Providers\SampleBaseServiceProvider::class,
];

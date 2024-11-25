<?php

return [

    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    App\Providers\SecurityServiceProvider::class,

    Src\V1\Web\Providers\Filament\AdminPanelProvider::class,
    Src\V1\Web\Providers\Filament\FilamentServiceProvider::class,

    Src\V1\Api\Auth\Providers\AuthServiceProvider::class,
    Src\V1\Api\User\Providers\UserServiceProvider::class,
];

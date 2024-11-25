<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
->withCommands([

    //
])
->withRouting(

    api: __DIR__."/../routes/api.php", apiPrefix: "api",
    web: __DIR__."/../routes/web.php",
    health: "/status",
)
->withBroadcasting(

    channels: __DIR__."/../routes/channels.php",
)
->withMiddleware(function (Middleware $middleware) {

    $middleware->group("api", [

        \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ]);
})
->withExceptions(function (Exceptions $exceptions) {

    //

})->create();

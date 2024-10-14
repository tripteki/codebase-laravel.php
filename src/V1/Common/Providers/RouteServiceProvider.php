<?php

namespace Src\V1\Common\Providers;

use Src\V1\Common\Traits\ServiceProviderTrait;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as BaseServiceProvider;

class RouteServiceProvider extends BaseServiceProvider
{
    use ServiceProviderTrait;

    /**
     * @var array
     */
    protected $mapMiddlewareBroadcast = [

        "auth:api",
        "verified",
    ];

    /**
     * @var array
     */
    protected $mapMiddlewareApi = [

        "api",
    ];

    /**
     * @var array
     */
    protected $mapMiddlewareWeb = [

        "web",
    ];

    /**
     * @return void
     */
    public function map()
    {
        $this->registerBroadcastRoutes();
        $this->registerApiRoutes();
        $this->registerWebRoutes();
    }

    /**
     * @return void
     */
    protected function registerBroadcastRoutes()
    {
        Broadcast::routes(
        [
            "prefix" => "api/".$this->versionLower()."/".$this->moduleLower(),
            "middleware" => $this->mapMiddlewareBroadcast,
        ]);

        require $this->modulePath("/Routes/channels.php");
    }

    /**
     * @return void
     */
    protected function registerApiRoutes()
    {
        Route::prefix("api/".$this->versionLower())->
        middleware($this->mapMiddlewareApi)->
        group($this->modulePath("/Routes/api.php"));
    }

    /**
     * @return void
     */
    protected function registerWebRoutes()
    {
        Route::prefix($this->versionLower())->
        middleware($this->mapMiddlewareWeb)->
        group($this->modulePath("/Routes/web.php"));
    }
};

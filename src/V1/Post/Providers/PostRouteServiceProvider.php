<?php

namespace Src\V1\Post\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class PostRouteServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    protected $moduleNamespace = "Src\V1\Post\Http\Controllers";

    /**
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
    }

    /**
     * @return void
     */
    protected function mapBroadcastRoutes()
    {
        Broadcast::routes(["prefix" => "api/v1/posts", "middleware" => "auth:api"]);

        require(module_path(PostBaseServiceProvider::$version, PostBaseServiceProvider::$moduleName."/".$this->getBroadcastRoutesPath()));
    }

    /**
     * @return string
     */
    protected function getBroadcastRoutesPath()
    {
        return "/".$this->app["modules"]->config("stubs.files.routes/broadcast", "Routes/_channel.php");
    }

    /**
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix("api/v1")->middleware("api")->namespace($this->moduleNamespace)->group(module_path(PostBaseServiceProvider::$version, PostBaseServiceProvider::$moduleName."/Routes/api.php"));
    }

    /**
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::prefix("v1")->middleware("web")->namespace($this->moduleNamespace)->group(module_path(PostBaseServiceProvider::$version, PostBaseServiceProvider::$moduleName."/Routes/web.php"));
    }
};

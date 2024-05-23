<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot()
    {
        Broadcast::routes(["prefix" => "api", "middleware" => "auth:api"]);

        require base_path("routes/_channel.php");
    }
}

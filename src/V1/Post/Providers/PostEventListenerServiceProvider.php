<?php

namespace Src\V1\Post\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class PostEventListenerServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $listen = [

        \Src\V1\Post\Events\PostCreated::class => [

            \Src\V1\Post\Listeners\PostSubscription::class
        ],
    ];

    /**
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
};

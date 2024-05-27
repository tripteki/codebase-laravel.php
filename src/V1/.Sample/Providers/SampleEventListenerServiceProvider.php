<?php

namespace Src\V1\Sample\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class SampleEventListenerServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $listen = [

        \Src\V1\Sample\Events\SampleCreated::class => [

            \Src\V1\Sample\Listeners\SampleSubscription::class
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

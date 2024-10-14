<?php

namespace Src\V1\Sample\Providers;

use Src\V1\Common\Providers\EventListenerServiceProvider as BaseServiceProvider;

class SampleEventListenerServiceProvider extends BaseServiceProvider
{
    /**
     * @var array
     */
    protected $listen = [

        \Src\V1\Sample\Events\SampleCreated::class => [

            \Src\V1\Sample\Listeners\SampleSubscription::class
        ],
    ];
};

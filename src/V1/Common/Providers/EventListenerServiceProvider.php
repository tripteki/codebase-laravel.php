<?php

namespace Src\V1\Common\Providers;

use Src\V1\Common\Traits\ServiceProviderTrait;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as BaseServiceProvider;

class EventListenerServiceProvider extends BaseServiceProvider
{
    use ServiceProviderTrait;

    /**
     * @var array
     */
    protected $listen = [

        //
    ];

    /**
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
};

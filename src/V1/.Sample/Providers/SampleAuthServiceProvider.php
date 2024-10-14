<?php

namespace Src\V1\Sample\Providers;

use Src\V1\Sample\Models\SampleModel;
use Src\V1\Sample\Policies\SamplePolicy;
use Src\V1\Common\Providers\ServiceProvider as BaseServiceProvider;
use Illuminate\Support\Facades\Gate;

class SampleAuthServiceProvider extends BaseServiceProvider
{
    /**
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->applyModuleName("Sample");
    }

    /**
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Gate::policy(SampleModel::class, SamplePolicy::class);
    }
};

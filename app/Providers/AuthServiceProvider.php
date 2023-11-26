<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    protected $policies = [];

    /**
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        $this->authenticate();
        $this->authorize();
    }

    /**
     * @return void
     */
    protected function authenticate()
    {
    }

    /**
     * @return void
     */
    protected function authorize()
    {
    }
}

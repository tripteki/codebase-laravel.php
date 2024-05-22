<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
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
        $this->registerRoutes();
    }

    /**
     * @return void
     */
    protected function registerRoutes()
    {
        ResetPassword::createUrlUsing(function ($notifiable, $token) {
            return config("app.frontend_url")."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });
    }
}

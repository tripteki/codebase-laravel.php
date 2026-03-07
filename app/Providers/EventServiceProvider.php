<?php

namespace App\Providers;

use App\Listeners\SendEmailVerificationNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Attempting;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Validated;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\CurrentDeviceLogout;
use Illuminate\Auth\Events\OtherDeviceLogout;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\URL;

class EventServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [

        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        Attempting::class => [
            //
        ],

        Authenticated::class => [
            //
        ],

        Login::class => [
            //
        ],

        Failed::class => [
            //
        ],

        Validated::class => [
            //
        ],

        Verified::class => [
            //
        ],

        Logout::class => [
            //
        ],

        CurrentDeviceLogout::class => [
            //
        ],

        OtherDeviceLogout::class => [
            //
        ],

        Lockout::class => [
            //
        ],

        PasswordReset::class => [
            //
        ],
    ];

    /**
     * @return void
     */
    public function boot(): void
    {
        VerifyEmail::createUrlUsing(function ($notifiable): string {
            $id = $notifiable->getKey();
            $hash = sha1($notifiable->getEmailForVerification());

            return URL::temporarySignedRoute(
                "verification.verify",
                now()->addMinutes(60),
                [
                    "id" => $id,
                    "hash" => $hash,
                ]
            );
        });

        ResetPassword::createUrlUsing(function ($notifiable, $token): string {
            return url(tenant_routes("admin.password.reset", [
                "token" => $token,
                "email" => $notifiable->getEmailForPasswordReset(),
            ], false));
        });
    }

    /**
     * @return bool
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}

<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Notification\App\Listeners\LogWebPushNotificationFailed;
use NotificationChannels\WebPush\Events\NotificationFailed;

class EventServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [

        NotificationFailed::class => [
            LogWebPushNotificationFailed::class,
        ],
    ];

    /**
     * @return void
     */
    public function boot(): void
    {
        //
    }

    /**
     * @return bool
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}

<?php

namespace App\Providers;

use App\Exceptions\Handler;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Modules\Notification\App\Listeners\LogWebPushNotificationFailed;
use NotificationChannels\WebPush\Events\NotificationFailed;

class AppServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(ExceptionHandler::class, Handler::class);
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        Event::listen(NotificationFailed::class, LogWebPushNotificationFailed::class);

        View::prependNamespace(
            "l5-swagger",
            resource_path("views/vendor/l5-swagger")
        );
    }
}

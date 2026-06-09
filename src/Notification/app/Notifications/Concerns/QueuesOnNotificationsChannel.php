<?php

namespace Modules\Notification\App\Notifications\Concerns;

trait QueuesOnNotificationsChannel
{
    /**
     * @return void
     */
    protected function queueOnNotificationsChannel(): void
    {
        $queue = config("webpush.notification_queue");

        if (filled($queue)) {
            $this->onQueue((string) $queue);
        }
    }
}

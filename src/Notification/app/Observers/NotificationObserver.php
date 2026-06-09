<?php

namespace Modules\Notification\App\Observers;

use App\Models\User;
use Modules\Notification\App\Events\NotificationCreated;
use Modules\Notification\App\Models\Notification;
use Modules\Notification\App\Notifications\InAppWebPushNotification;

class NotificationObserver
{
    /**
     * @param \Modules\Notification\App\Models\Notification $notification
     * @return void
     */
    public function created(Notification $notification): void
    {
        if ($notification->notifiable_type !== User::class) {
            return;
        }

        $user = User::query()->find($notification->notifiable_id);

        if ($user === null) {
            return;
        }

        event(new NotificationCreated(
            (string) $notification->notifiable_id,
            (string) $notification->id,
            $user->unreadNotifications()->count(),
        ));

        $this->dispatchWebPush($user, $notification);
    }

    /**
     * @param \App\Models\User $user
     * @param \Modules\Notification\App\Models\Notification $notification
     * @return void
     */
    protected function dispatchWebPush(User $user, Notification $notification): void
    {
        if (! filled(config("webpush.vapid.public_key"))) {
            return;
        }

        if (! $user->pushSubscriptions()->exists()) {
            return;
        }

        $data = is_array($notification->data) ? $notification->data : (array) $notification->data;

        $user->notify(new InAppWebPushNotification(
            notificationId: (string) $notification->id,
            type: (string) $notification->type,
            data: $data,
        ));
    }
}

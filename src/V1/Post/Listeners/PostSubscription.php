<?php

namespace Src\V1\Post\Listeners;

use App\Models\User as UserModel;
use Illuminate\Support\Facades\Notification;
use Illuminate\Contracts\Queue\ShouldQueue as IAsync;

class PostSubscription implements IAsync
{
    /**
     * @param mixed $data
     * @return void
     */
    public function handle($event)
    {
        $event = $event->data;
        $data = $event->response()->getData()->data;

        $from = $data->user;
        $to = UserModel::all()->except($from->id);

        $message = __("notification.ok");

        if ($to->isNotEmpty()) Notification::send($to, new \Src\V1\Post\Notifications\PostSubscriptionNotification($message, $event));
    }
};

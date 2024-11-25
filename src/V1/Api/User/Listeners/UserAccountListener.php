<?php

namespace Src\V1\Api\User\Listeners;

use App\Models\User;
use Src\V1\Api\User\Events\UserActivatedEvent;
use Src\V1\Api\User\Events\UserDeactivatedEvent;
use Src\V1\Api\User\Notifications\UserAccountNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class UserAccountListener implements ShouldQueue
{
    /**
     * @param \Src\V1\Api\User\Events\UserActivatedEvent|\Src\V1\Api\User\Events\UserDeactivatedEvent $userEvent
     * @return void
     */
    public function handle(UserActivatedEvent|UserDeactivatedEvent $userEvent): void
    {
        $eventFrom = Auth::user();
        $eventTos = User::where("id", $userEvent->user->id)->get();

        Notification::send($eventTos, new UserAccountNotification($userEvent));
    }
}

<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;

class SendEmailVerificationNotification
{
    /**
     * @param \Illuminate\Auth\Events\Registered $event
     * @return void
     */
    public function handle(Registered $event): void
    {
        $event->user->markEmailAsVerified();
        $event->user->sendEmailVerificationNotification();
    }
}

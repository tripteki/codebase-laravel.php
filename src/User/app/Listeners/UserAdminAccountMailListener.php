<?php

namespace Modules\User\App\Listeners;

use Illuminate\Support\Facades\Mail;
use Modules\Auth\App\Mail\UserAccountMail;
use Modules\User\App\Events\UserAdminActivated;
use Modules\User\App\Events\UserAdminDeactivated;

class UserAdminAccountMailListener
{
    /**
     * @param \Modules\User\App\Events\UserAdminActivated $event
     * @return void
     */
    public function handleActivated(UserAdminActivated $event): void
    {
        Mail::to($event->user->email)->send(new UserAccountMail(
            userName: $event->user->name,
            userEmail: $event->user->email,
            subjectKey: "auth.account_activated_subject",
            headingKey: "auth.account_activated_heading",
            lineKey: "auth.account_activated_line",
        ));
    }

    /**
     * @param \Modules\User\App\Events\UserAdminDeactivated $event
     * @return void
     */
    public function handleDeactivated(UserAdminDeactivated $event): void
    {
        Mail::to($event->user->email)->send(new UserAccountMail(
            userName: $event->user->name,
            userEmail: $event->user->email,
            subjectKey: "auth.account_deactivated_subject",
            headingKey: "auth.account_deactivated_heading",
            lineKey: "auth.account_deactivated_line",
        ));
    }
}

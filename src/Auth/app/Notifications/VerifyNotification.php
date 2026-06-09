<?php

namespace Modules\Auth\App\Notifications;

use App\Models\User;
use Illuminate\Notifications\Notification;
use Modules\Auth\App\Mail\VerifyEmailMail;

class VerifyNotification extends Notification
{
    /**
     * @param \App\Models\User $notifiable
     * @return array<int, string>
     */
    public function via(User $notifiable): array
    {
        return ["mail"];
    }

    /**
     * @param \App\Models\User $notifiable
     * @return \Modules\Auth\App\Mail\VerifyEmailMail
     */
    public function toMail(User $notifiable): VerifyEmailMail
    {
        $email = $notifiable->getEmailForVerification();

        return (new VerifyEmailMail(
            userName: $notifiable->displayName(),
            userNameLabel: $notifiable->displayNameLabel(),
            userEmail: $email,
            verificationUrl: signed_frontend_url(auth_verify_email_path($email)),
        ))->to($email);
    }
}

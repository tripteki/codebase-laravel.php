<?php

namespace App\Notifications\Channels;

use App\Models\Tenant;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Mail;

class MailChannel
{
    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     * @return void
     */
    public function send($notifiable, Notification $notification): void
    {
        $message = $notification->toMail($notifiable);
        if (! $message instanceof Mailable) {
            return;
        }

        $email = $notifiable->routeNotificationFor('mail', $notification);
        if (! $email) {
            return;
        }

        $tenant = null;
        if (config("tenancy.is_tenancy") && isset($notifiable->tenant_id) && filled($notifiable->tenant_id)) {
            $tenant = Tenant::query()->find($notifiable->tenant_id);
        }

        if ($tenant !== null) {
            [$mailer] = tenant_mailers($tenant);
            $mailer->to($email)->send($message);
        } else {
            Mail::to($email)->send($message);
        }
    }
}

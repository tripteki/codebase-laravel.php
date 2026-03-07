<?php

namespace App\Notifications;

use App\Enum\Event\AddOnEnum;
use App\Helpers\AddOnsHelper;
use App\Mail\VerifyEmailMail;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class VerifyNotification extends Notification
{
    /**
     * @return string
     */
    public function broadcastType(): string
    {
        return "user.verify";
    }

    /**
     * @param \App\Models\User $notifiable
     * @return array<string|class-string>
     */
    public function via(User $notifiable): array
    {
        $channels = ["database", "broadcast", WebPushChannel::class];

        $includeMail = true;
        if (config("tenancy.is_tenancy") && filled($notifiable->tenant_id)) {
            $tenant = Tenant::query()->find($notifiable->tenant_id);
            $includeMail = $tenant !== null && in_array(
                AddOnEnum::FEATURES_MAILING,
                AddOnsHelper::enabledFeatureCases($tenant),
                true
            );
        }
        if ($includeMail) {
            array_unshift($channels, Channels\MailChannel::class);
        }

        return $channels;
    }

    /**
     * @param \App\Models\User $notifiable
     * @return \App\Mail\VerifyEmailMail
     */
    public function toMail(User $notifiable): VerifyEmailMail
    {
        $appName = config("app.name");
        $logoUrl = null;
        $primaryColor = null;
        $secondaryColor = null;
        $tertiaryColor = null;
        $eventStart = null;
        $eventEnd = null;
        $eventDescription = null;
        $fromAddress = null;
        $fromName = null;

        if (config("tenancy.is_tenancy") && filled($notifiable->tenant_id)) {
            $tenant = Tenant::query()->find($notifiable->tenant_id);
            if ($tenant) {
                tenancy()->initialize($tenant);
                $appName = (string) (tenant("title") ?? $appName);
                $logoUrl = tenant("icon") ? asset("storage/" . tenant("icon")) : null;
                $primaryColor = tenant("primary_color") ? (string) tenant("primary_color") : null;
                $secondaryColor = tenant("secondary_color") ? (string) tenant("secondary_color") : null;
                $tertiaryColor = tenant("tertiary_color") ? (string) tenant("tertiary_color") : null;
                $eventStart = $notifiable->formatEventDateTime(
                    $tenant->getAttribute("event_start_date"),
                    $tenant->getAttribute("event_start_time")
                );
                $eventEnd = $notifiable->formatEventDateTime(
                    $tenant->getAttribute("event_end_date"),
                    $tenant->getAttribute("event_end_time")
                );
                $eventDescription = trim((string) ($tenant->getAttribute("description") ?? "")) ?: null;
                [, $fromAddress, $fromName] = tenant_mailers($tenant);
                tenancy()->end();
            }
        }

        if ($logoUrl === null) {
            $logoUrl = asset("asset/logo.png");
        }

        $userName = $notifiable->profile?->full_name ?? $notifiable->name;
        $userEmail = $notifiable->getEmailForVerification();

        return new VerifyEmailMail(
            $appName,
            $logoUrl,
            $primaryColor,
            $secondaryColor,
            $tertiaryColor,
            $eventStart,
            $eventEnd,
            $eventDescription,
            $userName,
            $userEmail,
            $fromAddress,
            $fromName
        );
    }

    /**
     * @param \App\Models\User $notifiable
     * @return array{title: string, body: string}
     */
    private function notificationTitleAndBody(User $notifiable): array
    {
        $title = __("auth.email_registration_confirmed");
        $userName = $notifiable->profile?->full_name ?? $notifiable->name;
        $body = $userName
            ? __("auth.email_hi") . " " . $userName . ", " . __("auth.email_thank_you_register")
            : __("auth.hi_there") . " " . __("auth.email_thank_you_register");

        return ["title" => $title, "body" => $body];
    }

    /**
     * @param \App\Models\User $notifiable
     * @return \Illuminate\Notifications\Messages\DatabaseMessage
     */
    public function toDatabase(User $notifiable): DatabaseMessage
    {
        $data = $this->notificationTitleAndBody($notifiable);
        $data["type"] = "email_verification";

        return new DatabaseMessage($data);
    }

    /**
     * @param \App\Models\User $notifiable
     * @return \Illuminate\Notifications\Messages\BroadcastMessage
     */
    public function toBroadcast(User $notifiable): BroadcastMessage
    {
        $data = $this->notificationTitleAndBody($notifiable);
        $data["type"] = "email_verification";

        return new BroadcastMessage($data);
    }

    /**
     * @param \App\Models\User $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     * @return \NotificationChannels\WebPush\WebPushMessage
     */
    public function toWebPush(User $notifiable, $notification): WebPushMessage
    {
        $data = $this->notificationTitleAndBody($notifiable);

        return (new WebPushMessage)
            ->title($data["title"])
            ->body($data["body"])
            ->icon("/favicon.ico")
            ->data([
                "type" => "email_verification",
            ]);
    }
}

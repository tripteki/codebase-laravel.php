<?php

namespace App\Notifications;

use App\Models\StageMeeting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class StageMeetingExhibitorSponsorNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param \App\Models\StageMeeting $meeting
     * @param string|null $url
     * @return void
     */
    public function __construct(
        public StageMeeting $meeting,
        public ?string $url = null
    ) {
    }

    /**
     * @return string
     */
    public function broadcastType(): string
    {
        return "stage.meeting.exhibitor_sponsor";
    }

    /**
     * @param mixed $notifiable
     * @return array<string>
     */
    public function via($notifiable): array
    {
        return [
            "database",
            "broadcast",
            WebPushChannel::class,
        ];
    }

    /**
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\DatabaseMessage
     */
    public function toDatabase($notifiable): DatabaseMessage
    {
        $roomId = "#" . strtoupper((string) ($this->meeting->room_id ?? substr($this->meeting->id, 0, 6)));
        $title = __("module_stage.notification_meeting_exhibitor_sponsor_title", ["room_id" => $roomId]);
        $body = __("module_stage.notification_meeting_exhibitor_sponsor_body");

        return new DatabaseMessage([
            "title" => $title,
            "body" => $body,
            "meeting_id" => $this->meeting->id,
            "type" => "stage_meeting_exhibitor_sponsor",
            "url" => $this->url,
            "icon" => "meeting",
        ]);
    }

    /**
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\BroadcastMessage
     */
    public function toBroadcast($notifiable): BroadcastMessage
    {
        $roomId = "#" . strtoupper((string) ($this->meeting->room_id ?? substr($this->meeting->id, 0, 6)));
        $title = __("module_stage.notification_meeting_exhibitor_sponsor_title", ["room_id" => $roomId]);
        $body = __("module_stage.notification_meeting_exhibitor_sponsor_body");

        return new BroadcastMessage([
            "title" => $title,
            "body" => $body,
            "meeting_id" => $this->meeting->id,
            "type" => "stage_meeting_exhibitor_sponsor",
            "url" => $this->url,
            "icon" => "meeting",
        ]);
    }

    /**
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     * @return \NotificationChannels\WebPush\WebPushMessage
     */
    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        $roomId = "#" . strtoupper((string) ($this->meeting->room_id ?? substr($this->meeting->id, 0, 6)));
        $title = __("module_stage.notification_meeting_exhibitor_sponsor_title", ["room_id" => $roomId]);
        $body = __("module_stage.notification_meeting_exhibitor_sponsor_body");

        return (new WebPushMessage)
            ->title($title)
            ->body($body)
            ->icon("/favicon.ico")
            ->data([
                "meeting_id" => $this->meeting->id,
                "type" => "stage_meeting_exhibitor_sponsor",
                "url" => $this->url,
                "icon" => "meeting",
            ]);
    }
}

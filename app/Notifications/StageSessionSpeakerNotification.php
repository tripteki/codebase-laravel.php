<?php

namespace App\Notifications;

use App\Models\StageSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class StageSessionSpeakerNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param \App\Models\StageSession $session
     * @param string|null $url
     * @return void
     */
    public function __construct(
        public StageSession $session,
        public ?string $url = null
    ) {
    }

    /**
     * @return string
     */
    public function broadcastType(): string
    {
        return "stage.session.speaker";
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
        $roomId = "#" . strtoupper((string) ($this->session->room_id ?? substr($this->session->id, 0, 6)));
        $title = __("module_stage.notification_session_speaker_title", ["room_id" => $roomId]);
        $body = __("module_stage.notification_session_speaker_body");

        return new DatabaseMessage([
            "title" => $title,
            "body" => $body,
            "session_id" => $this->session->id,
            "type" => "stage_session_speaker",
            "url" => $this->url,
            "icon" => "session",
        ]);
    }

    /**
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\BroadcastMessage
     */
    public function toBroadcast($notifiable): BroadcastMessage
    {
        $roomId = "#" . strtoupper((string) ($this->session->room_id ?? substr($this->session->id, 0, 6)));
        $title = __("module_stage.notification_session_speaker_title", ["room_id" => $roomId]);
        $body = __("module_stage.notification_session_speaker_body");

        return new BroadcastMessage([
            "title" => $title,
            "body" => $body,
            "session_id" => $this->session->id,
            "type" => "stage_session_speaker",
            "url" => $this->url,
            "icon" => "session",
        ]);
    }

    /**
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     * @return \NotificationChannels\WebPush\WebPushMessage
     */
    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        $roomId = "#" . strtoupper((string) ($this->session->room_id ?? substr($this->session->id, 0, 6)));
        $title = __("module_stage.notification_session_speaker_title", ["room_id" => $roomId]);
        $body = __("module_stage.notification_session_speaker_body");

        return (new WebPushMessage)
            ->title($title)
            ->body($body)
            ->icon("/favicon.ico")
            ->data([
                "session_id" => $this->session->id,
                "type" => "stage_session_speaker",
                "url" => $this->url,
                "icon" => "session",
            ]);
    }
}

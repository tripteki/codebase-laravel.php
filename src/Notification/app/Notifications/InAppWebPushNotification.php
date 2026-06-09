<?php

namespace Modules\Notification\App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Modules\Notification\App\Notifications\Concerns\QueuesOnNotificationsChannel;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class InAppWebPushNotification extends Notification implements ShouldQueue
{
    use Queueable, QueuesOnNotificationsChannel;

    /**
     * @param string $notificationId
     * @param string $type
     * @param array<string, mixed> $data
     * @return void
     */
    public function __construct(
        public string $notificationId,
        public string $type,
        public array $data,
    ) {
        $this->queueOnNotificationsChannel();
    }

    /**
     * @param mixed $notifiable
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return [ WebPushChannel::class, ];
    }

    /**
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     * @return \NotificationChannels\WebPush\WebPushMessage
     */
    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        $title = $this->resolveTitle();
        $body = $this->resolveBody();
        $url = $this->resolveUrl();
        $downloadUrl = $this->resolveDownloadUrl();

        $payload = array_merge($this->data, [
            "url" => $url,
            "notification_id" => $this->notificationId,
            "type" => $this->type,
        ]);

        $message = (new WebPushMessage)
            ->title($title)
            ->body($body)
            ->icon("/manifest/icon-512x512.png")
            ->badge("/manifest/icon-192x192.png")
            ->data($payload)
            ->tag("notification_{$this->notificationId}");

        if ($downloadUrl !== null) {
            $message->action((string) __("notification.webpush.download"), $downloadUrl);
        } elseif ($url !== null) {
            $message->action((string) __("notification.webpush.open"), $url);
        }

        return $message;
    }

    /**
     * @return string
     */
    protected function resolveTitle(): string
    {
        $title = trim((string) ($this->data["title"] ?? ""));

        if ($title !== "") {
            return $title;
        }

        $message = trim((string) ($this->data["message"] ?? ""));

        if ($message !== "") {
            return $message;
        }

        return $this->type;
    }

    /**
     * @return string
     */
    protected function resolveBody(): string
    {
        $lines = [];

        $headline = trim((string) ($this->data["body_primary"] ?? ""));

        if ($headline !== "") {
            $lines[] = $headline;
        } elseif (filled($this->data["filename"] ?? null)) {
            $lines[] = trim((string) $this->data["filename"]);
        } else {
            $message = trim((string) ($this->data["message"] ?? ""));
            $title = trim((string) ($this->data["title"] ?? ""));

            if ($message !== "" && $title !== "" && $message !== $title) {
                $lines[] = $message;
            }
        }

        $secondary = trim((string) ($this->data["body_secondary"] ?? ""));

        if ($secondary !== "") {
            $lines[] = $secondary;
        } elseif (filled($this->data["error"] ?? null)) {
            $lines[] = trim((string) $this->data["error"]);
        }

        $presentationLines = $this->data["presentation_lines"] ?? [];

        if (is_array($presentationLines)) {
            foreach ($presentationLines as $line) {
                if (! is_string($line) || trim($line) === "") {
                    continue;
                }

                $lines[] = trim($line);

                if (count($lines) >= 4) {
                    break;
                }
            }
        }

        if ($lines !== []) {
            return implode("\n", $lines);
        }

        $totalImported = $this->data["totalImported"] ?? null;
        $totalSkipped = $this->data["totalSkipped"] ?? null;

        if ($totalImported !== null) {
            $summary = __("notification.webpush.import_summary", [
                "imported" => number_format((int) $totalImported),
                "skipped" => number_format((int) ($totalSkipped ?? 0)),
            ]);

            if ($summary !== "notification.webpush.import_summary") {
                return $summary;
            }
        }

        return $this->resolveTitle();
    }

    /**
     * @return string|null
     */
    protected function resolveUrl(): ?string
    {
        foreach ([ "pdf_url", "fileUrl", "url", ] as $key) {
            $value = $this->data[$key] ?? null;

            if (! is_string($value) || trim($value) === "") {
                continue;
            }

            return trim($value);
        }

        return frontend_url("notifications");
    }

    /**
     * @return string|null
     */
    protected function resolveDownloadUrl(): ?string
    {
        $fileUrl = $this->data["fileUrl"] ?? null;

        if (! is_string($fileUrl) || trim($fileUrl) === "") {
            return null;
        }

        return trim($fileUrl);
    }
}

<?php

namespace App\Notifications;

use App\Models\Export;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class ExportCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\Export $export
     */
    public function __construct(
        public Export $export
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array<string>
     */
    public function via($notifiable): array
    {
        return [
            "database",
            "broadcast",
        ];
    }

    /**
     * Get the database representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\DatabaseMessage
     */
    public function toDatabase($notifiable): DatabaseMessage
    {
        $successfulRows = $this->export->successful_rows;
        $failedRows = $this->export->getFailedRowsCount();

        $body = __("notifications.export.completed", [
            "successfulRows" => number_format($successfulRows),
        ]);

        if ($failedRows > 0) {
            $body .= " " . __("notifications.export.failed", [
                "failedRows" => number_format($failedRows),
            ]);
        }

        $filePath = "exports/" . $this->export->file_name;

        return new DatabaseMessage([
            "title" => __("module_base.export_completed"),
            "body" => $body,
            "export_id" => $this->export->id,
            "file_name" => $this->export->file_name,
            "file_url" => $filePath,
            "successful_rows" => $successfulRows,
            "failed_rows" => $failedRows,
        ]);
    }

    /**
     * Get the broadcast representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\BroadcastMessage
     */
    public function toBroadcast($notifiable): BroadcastMessage
    {
        $successfulRows = $this->export->successful_rows;
        $failedRows = $this->export->getFailedRowsCount();

        $body = __("notifications.export.completed", [
            "successfulRows" => number_format($successfulRows),
        ]);

        if ($failedRows > 0) {
            $body .= " " . __("notifications.export.failed", [
                "failedRows" => number_format($failedRows),
            ]);
        }

        $filePath = "exports/" . $this->export->file_name;

        return new BroadcastMessage([
            "title" => __("module_base.export_completed"),
            "body" => $body,
            "export_id" => $this->export->id,
            "file_name" => $this->export->file_name,
            "file_url" => $filePath,
            "successful_rows" => $successfulRows,
            "failed_rows" => $failedRows,
        ]);
    }

    /**
     * Get the type of the notification being broadcast.
     *
     * @return string
     */
    public function broadcastType(): string
    {
        return "export.completed";
    }
}

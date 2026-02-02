<?php

namespace App\Notifications;

use App\Models\Import;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class ImportCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\Import $import
     */
    public function __construct(
        public Import $import
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
        $this->import->refresh();

        $successfulRows = $this->import->successful_rows;
        $failedRows = $this->import->getFailedRowsCount();

        if ($failedRows === 0 && $this->import->total_rows > $successfulRows) {
            $failedRows = $this->import->total_rows - $successfulRows;
        }

        $body = __("notifications.import.completed", [
            "successfulRows" => number_format($successfulRows),
        ]);

        if ($failedRows > 0) {
            $body .= " " . __("notifications.import.failed", [
                "failedRows" => number_format($failedRows),
            ]);
        }

        return new DatabaseMessage([
            "title" => __("module_base.import_completed"),
            "body" => $body,
            "import_id" => $this->import->id,
            "file_name" => $this->import->file_name,
            "url" => $this->import->file_path,
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
        $this->import->refresh();

        $successfulRows = $this->import->successful_rows;
        $failedRows = $this->import->getFailedRowsCount();

        if ($failedRows === 0 && $this->import->total_rows > $successfulRows) {
            $failedRows = $this->import->total_rows - $successfulRows;
        }

        $body = __("notifications.import.completed", [
            "successfulRows" => number_format($successfulRows),
        ]);

        if ($failedRows > 0) {
            $body .= " " . __("notifications.import.failed", [
                "failedRows" => number_format($failedRows),
            ]);
        }

        return new BroadcastMessage([
            "title" => __("module_base.import_completed"),
            "body" => $body,
            "import_id" => $this->import->id,
            "file_name" => $this->import->file_name,
            "url" => $this->import->file_path,
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
        return "import.completed";
    }
}

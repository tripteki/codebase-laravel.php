<?php

namespace App\Notifications;

use App\Models\Export;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class ExportCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param \App\Models\Export $export
     * @return void
     */
    public function __construct(
        public Export $export
    ) {
    }

    /**
     * @return string
     */
    public function broadcastType(): string
    {
        return "export.completed";
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
            "url" => $filePath,
            "successful_rows" => $successfulRows,
            "failed_rows" => $failedRows,
            "icon" => "export",
            "refresh_datatables" => true,
        ]);
    }

    /**
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
            "url" => $filePath,
            "successful_rows" => $successfulRows,
            "failed_rows" => $failedRows,
            "icon" => "export",
            "refresh_datatables" => true,
        ]);
    }

    /**
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     * @return \NotificationChannels\WebPush\WebPushMessage
     */
    public function toWebPush($notifiable, $notification): WebPushMessage
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
        $downloadUrl = null;

        if ($filePath) {
            $storagePath = storage_path('app/public/' . $filePath);
            if (file_exists($storagePath)) {
                $downloadUrl = asset('storage/' . $filePath);
            }
        }

        $webPushMessage = (new WebPushMessage)
            ->title(__("module_base.export_completed"))
            ->body($body)
            ->icon('/favicon.ico')
            ->data([
                'export_id' => $this->export->id,
                'file_name' => $this->export->file_name,
                'file_path' => $filePath,
                'url' => $downloadUrl ?: null,
                'successful_rows' => $successfulRows,
                'failed_rows' => $failedRows,
                'icon' => 'export',
                'refresh_datatables' => true,
            ]);

        if ($downloadUrl) {
            $webPushMessage->action(__("common.download"), $downloadUrl);
        }

        return $webPushMessage;
    }
}

<?php

namespace Modules\Event\App\Listeners;

use App\Listeners\Concerns\StoresAdminOperationNotification;
use Modules\Event\App\Events\EventAdminExported;
use Modules\Event\App\Events\EventAdminExportedFailed;
use Modules\Event\App\Events\EventAdminImported;
use Modules\Event\App\Events\EventAdminImportedFailed;

class EventAdminOperationDatabaseListener
{
    use StoresAdminOperationNotification;

    /**
     * @param EventAdminImported $event
     * @return void
     */
    public function handleImported(EventAdminImported $event): void
    {
        $this->storeAdminOperationNotification($event->userId, "event.import.completed", [
            "title" => $event->message,
            "filename" => $event->filename,
            "message" => $event->message,
            "presentation_icon" => "import",
            "totalImported" => $event->totalImported,
            "totalSkipped" => $event->totalSkipped,
        ]);
    }

    /**
     * @param EventAdminImportedFailed $event
     * @return void
     */
    public function handleImportedFailed(EventAdminImportedFailed $event): void
    {
        $this->storeAdminOperationNotification($event->userId, "event.import.failed", [
            "title" => $event->message,
            "filename" => $event->filename,
            "message" => $event->message,
            "presentation_icon" => "import",
            "error" => $event->error,
        ]);
    }

    /**
     * @param EventAdminExported $event
     * @return void
     */
    public function handleExported(EventAdminExported $event): void
    {
        $this->storeAdminOperationNotification($event->userId, "event.export.completed", [
            "title" => $event->message,
            "filename" => $event->filename,
            "fileUrl" => $event->fileUrl,
            "message" => $event->message,
            "presentation_icon" => "export",
        ]);
    }

    /**
     * @param EventAdminExportedFailed $event
     * @return void
     */
    public function handleExportedFailed(EventAdminExportedFailed $event): void
    {
        $this->storeAdminOperationNotification($event->userId, "event.export.failed", [
            "title" => $event->message,
            "message" => $event->message,
            "presentation_icon" => "export",
            "error" => $event->error,
        ]);
    }
}

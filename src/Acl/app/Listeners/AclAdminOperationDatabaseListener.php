<?php

namespace Modules\Acl\App\Listeners;

use App\Listeners\Concerns\StoresAdminOperationNotification;
use Modules\Acl\App\Events\PermissionAdminExported;
use Modules\Acl\App\Events\PermissionAdminExportedFailed;
use Modules\Acl\App\Events\PermissionAdminImported;
use Modules\Acl\App\Events\PermissionAdminImportedFailed;
use Modules\Acl\App\Events\RoleAdminExported;
use Modules\Acl\App\Events\RoleAdminExportedFailed;
use Modules\Acl\App\Events\RoleAdminImported;
use Modules\Acl\App\Events\RoleAdminImportedFailed;

class AclAdminOperationDatabaseListener
{
    use StoresAdminOperationNotification;

    public function handleRoleImported(RoleAdminImported $event): void
    {
        $this->storeAdminOperationNotification($event->userId, "acl.role.import.completed", [
            "title" => $event->message,
            "filename" => $event->filename,
            "message" => $event->message,
            "presentation_icon" => "import",
            "totalImported" => $event->totalImported,
            "totalSkipped" => $event->totalSkipped,
        ]);
    }

    public function handleRoleImportedFailed(RoleAdminImportedFailed $event): void
    {
        $this->storeAdminOperationNotification($event->userId, "acl.role.import.failed", [
            "title" => $event->message,
            "filename" => $event->filename,
            "message" => $event->message,
            "presentation_icon" => "import",
            "error" => $event->error,
        ]);
    }

    public function handleRoleExported(RoleAdminExported $event): void
    {
        $this->storeAdminOperationNotification($event->userId, "acl.role.export.completed", [
            "title" => $event->message,
            "filename" => $event->filename,
            "fileUrl" => $event->fileUrl,
            "message" => $event->message,
            "presentation_icon" => "export",
        ]);
    }

    public function handleRoleExportedFailed(RoleAdminExportedFailed $event): void
    {
        $this->storeAdminOperationNotification($event->userId, "acl.role.export.failed", [
            "title" => $event->message,
            "message" => $event->message,
            "presentation_icon" => "export",
            "error" => $event->error,
        ]);
    }

    public function handlePermissionImported(PermissionAdminImported $event): void
    {
        $this->storeAdminOperationNotification($event->userId, "acl.permission.import.completed", [
            "title" => $event->message,
            "filename" => $event->filename,
            "message" => $event->message,
            "presentation_icon" => "import",
            "totalImported" => $event->totalImported,
            "totalSkipped" => $event->totalSkipped,
        ]);
    }

    public function handlePermissionImportedFailed(PermissionAdminImportedFailed $event): void
    {
        $this->storeAdminOperationNotification($event->userId, "acl.permission.import.failed", [
            "title" => $event->message,
            "filename" => $event->filename,
            "message" => $event->message,
            "presentation_icon" => "import",
            "error" => $event->error,
        ]);
    }

    public function handlePermissionExported(PermissionAdminExported $event): void
    {
        $this->storeAdminOperationNotification($event->userId, "acl.permission.export.completed", [
            "title" => $event->message,
            "filename" => $event->filename,
            "fileUrl" => $event->fileUrl,
            "message" => $event->message,
            "presentation_icon" => "export",
        ]);
    }

    public function handlePermissionExportedFailed(PermissionAdminExportedFailed $event): void
    {
        $this->storeAdminOperationNotification($event->userId, "acl.permission.export.failed", [
            "title" => $event->message,
            "message" => $event->message,
            "presentation_icon" => "export",
            "error" => $event->error,
        ]);
    }
}

<?php

namespace Modules\Acl\App\Listeners;

use App\Listeners\Concerns\StoresAdminOperationNotification;
use Modules\Log\App\Support\AdminRefreshBroadcaster;
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

    /**
     * @param \Modules\Acl\App\Events\RoleAdminImported $event
     * @return void
     */
    public function handleRoleImported(RoleAdminImported $event): void
    {
        $this->storeAdminOperationNotification($event->userId, "role.import.completed", [
            "filename" => $event->filename,
            "totalImported" => $event->totalImported,
            "totalSkipped" => $event->totalSkipped,
            "message" => $event->message,
        ]);

        if ($event->totalImported > 0) {
            AdminRefreshBroadcaster::broadcast([ "roles", ], $event->userId);
        }
    }

    /**
     * @param \Modules\Acl\App\Events\RoleAdminImportedFailed $event
     * @return void
     */
    public function handleRoleImportedFailed(RoleAdminImportedFailed $event): void
    {
        $this->storeAdminOperationNotification($event->userId, "role.import.failed", [
            "filename" => $event->filename,
            "message" => $event->message,
            "error" => $event->error,
        ]);
    }

    /**
     * @param \Modules\Acl\App\Events\RoleAdminExported $event
     * @return void
     */
    public function handleRoleExported(RoleAdminExported $event): void
    {
        $this->storeAdminOperationNotification($event->userId, "role.export.completed", [
            "filename" => $event->filename,
            "fileUrl" => $event->fileUrl,
            "message" => $event->message,
        ]);
    }

    /**
     * @param \Modules\Acl\App\Events\RoleAdminExportedFailed $event
     * @return void
     */
    public function handleRoleExportedFailed(RoleAdminExportedFailed $event): void
    {
        $this->storeAdminOperationNotification($event->userId, "role.export.failed", [
            "message" => $event->message,
            "error" => $event->error,
        ]);
    }

    /**
     * @param \Modules\Acl\App\Events\PermissionAdminImported $event
     * @return void
     */
    public function handlePermissionImported(PermissionAdminImported $event): void
    {
        $this->storeAdminOperationNotification($event->userId, "permission.import.completed", [
            "filename" => $event->filename,
            "totalImported" => $event->totalImported,
            "totalSkipped" => $event->totalSkipped,
            "message" => $event->message,
        ]);

        if ($event->totalImported > 0) {
            AdminRefreshBroadcaster::broadcast([ "permissions", ], $event->userId);
        }
    }

    /**
     * @param \Modules\Acl\App\Events\PermissionAdminImportedFailed $event
     * @return void
     */
    public function handlePermissionImportedFailed(PermissionAdminImportedFailed $event): void
    {
        $this->storeAdminOperationNotification($event->userId, "permission.import.failed", [
            "filename" => $event->filename,
            "message" => $event->message,
            "error" => $event->error,
        ]);
    }

    /**
     * @param \Modules\Acl\App\Events\PermissionAdminExported $event
     * @return void
     */
    public function handlePermissionExported(PermissionAdminExported $event): void
    {
        $this->storeAdminOperationNotification($event->userId, "permission.export.completed", [
            "filename" => $event->filename,
            "fileUrl" => $event->fileUrl,
            "message" => $event->message,
        ]);
    }

    /**
     * @param \Modules\Acl\App\Events\PermissionAdminExportedFailed $event
     * @return void
     */
    public function handlePermissionExportedFailed(PermissionAdminExportedFailed $event): void
    {
        $this->storeAdminOperationNotification($event->userId, "permission.export.failed", [
            "message" => $event->message,
            "error" => $event->error,
        ]);
    }
}

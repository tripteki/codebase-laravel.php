<?php

namespace Modules\User\App\Listeners;

use App\Models\User;
use Illuminate\Support\Str;
use Modules\User\App\Events\UserAdminExported;
use Modules\User\App\Events\UserAdminExportedFailed;
use Modules\User\App\Events\UserAdminImported;
use Modules\User\App\Events\UserAdminImportedFailed;

class UserAdminOperationDatabaseListener
{
    /**
     * @param \Modules\User\App\Events\UserAdminImported $event
     * @return void
     */
    public function handleImported(UserAdminImported $event): void
    {
        $this->storeNotification($event->userId, "user.import.completed", [
            "filename" => $event->filename,
            "totalImported" => $event->totalImported,
            "totalSkipped" => $event->totalSkipped,
            "message" => $event->message,
        ]);
    }

    /**
     * @param \Modules\User\App\Events\UserAdminImportedFailed $event
     * @return void
     */
    public function handleImportedFailed(UserAdminImportedFailed $event): void
    {
        $this->storeNotification($event->userId, "user.import.failed", [
            "filename" => $event->filename,
            "message" => $event->message,
            "error" => $event->error,
        ]);
    }

    /**
     * @param \Modules\User\App\Events\UserAdminExported $event
     * @return void
     */
    public function handleExported(UserAdminExported $event): void
    {
        $this->storeNotification($event->userId, "user.export.completed", [
            "filename" => $event->filename,
            "fileUrl" => $event->fileUrl,
            "message" => $event->message,
        ]);
    }

    /**
     * @param \Modules\User\App\Events\UserAdminExportedFailed $event
     * @return void
     */
    public function handleExportedFailed(UserAdminExportedFailed $event): void
    {
        $this->storeNotification($event->userId, "user.export.failed", [
            "message" => $event->message,
            "error" => $event->error,
        ]);
    }

    /**
     * @param string $userId
     * @param string $type
     * @param array<string, mixed> $data
     * @return void
     */
    protected function storeNotification(string $userId, string $type, array $data): void
    {
        $user = User::find($userId);

        if (! $user) {
            return;
        }

        $user->notifications()->create([
            "id" => Str::uuid()->toString(),
            "type" => $type,
            "data" => $data,
        ]);
    }
}

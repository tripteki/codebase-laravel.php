<?php

namespace Modules\Acl\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Acl\App\Events\PermissionAdminImported;
use Modules\Acl\App\Events\PermissionAdminImportedFailed;
use Modules\Acl\App\Repositories\PermissionAdminRepository;

class PermissionAdminImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $userId,
        public string $path,
        public string $filename,
    ) {
        $this->onQueue("acl-admin-queue");
    }

    public function handle(PermissionAdminRepository $permissionAdminRepository): void
    {
        try {
            $result = $permissionAdminRepository->importFromFile($this->path);

            event(new PermissionAdminImported(
                userId: $this->userId,
                filename: $this->filename,
                totalImported: $result["imported"],
                totalSkipped: $result["skipped"],
                message: __("acl.permission.import.completed"),
            ));
        } catch (\Throwable $exception) {
            event(new PermissionAdminImportedFailed(
                userId: $this->userId,
                filename: $this->filename,
                message: __("acl.permission.import.failed"),
                error: $exception->getMessage(),
            ));
        }
    }
}

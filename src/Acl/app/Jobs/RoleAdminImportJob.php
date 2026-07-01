<?php

namespace Modules\Acl\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Acl\App\Events\RoleAdminImported;
use Modules\Acl\App\Events\RoleAdminImportedFailed;
use Modules\Acl\App\Repositories\RoleAdminRepository;

class RoleAdminImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $userId,
        public string $path,
        public string $filename,
    ) {
        $this->onQueue("acl-admin-queue");
    }

    public function handle(RoleAdminRepository $roleAdminRepository): void
    {
        try {
            $result = $roleAdminRepository->importFromFile($this->path);

            event(new RoleAdminImported(
                userId: $this->userId,
                filename: $this->filename,
                totalImported: $result["imported"],
                totalSkipped: $result["skipped"],
                message: __("acl.role.import.completed"),
            ));
        } catch (\Throwable $exception) {
            event(new RoleAdminImportedFailed(
                userId: $this->userId,
                filename: $this->filename,
                message: __("acl.role.import.failed"),
                error: $exception->getMessage(),
            ));
        }
    }
}

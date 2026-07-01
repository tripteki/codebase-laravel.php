<?php

namespace Modules\Acl\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Modules\Acl\App\Events\PermissionAdminExported;
use Modules\Acl\App\Events\PermissionAdminExportedFailed;
use Modules\Acl\App\Repositories\PermissionAdminRepository;

class PermissionAdminExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $userId,
        public string $type = "csv",
        public array $filters = [],
    ) {
        $this->onQueue("acl-admin-queue");
    }

    public function handle(PermissionAdminRepository $permissionAdminRepository): void
    {
        try {
            $path = $permissionAdminRepository->exportToFile($this->type, $this->filters);
            $filename = basename($path);
            $relative = "exports/".$filename;
            $fileUrl = Storage::disk("public")->url($relative);

            event(new PermissionAdminExported(
                userId: $this->userId,
                filename: $filename,
                fileUrl: $fileUrl,
                filePath: $path,
                message: __("acl.permission.export.completed"),
            ));
        } catch (\Throwable $exception) {
            event(new PermissionAdminExportedFailed(
                userId: $this->userId,
                message: __("acl.permission.export.failed"),
                error: $exception->getMessage(),
            ));
        }
    }
}

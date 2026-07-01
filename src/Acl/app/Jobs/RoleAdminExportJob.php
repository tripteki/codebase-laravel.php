<?php

namespace Modules\Acl\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Modules\Acl\App\Events\RoleAdminExported;
use Modules\Acl\App\Events\RoleAdminExportedFailed;
use Modules\Acl\App\Repositories\RoleAdminRepository;

class RoleAdminExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $userId,
        public string $type = "csv",
        public array $filters = [],
    ) {
        $this->onQueue("acl-admin-queue");
    }

    public function handle(RoleAdminRepository $roleAdminRepository): void
    {
        try {
            $path = $roleAdminRepository->exportToFile($this->type, $this->filters);
            $filename = basename($path);
            $relative = "exports/".$filename;
            $fileUrl = Storage::disk("public")->url($relative);

            event(new RoleAdminExported(
                userId: $this->userId,
                filename: $filename,
                fileUrl: $fileUrl,
                filePath: $path,
                message: __("acl.role.export.completed"),
            ));
        } catch (\Throwable $exception) {
            event(new RoleAdminExportedFailed(
                userId: $this->userId,
                message: __("acl.role.export.failed"),
                error: $exception->getMessage(),
            ));
        }
    }
}

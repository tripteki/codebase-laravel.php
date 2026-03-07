<?php

namespace Modules\User\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Modules\User\App\Events\UserAdminExported;
use Modules\User\App\Events\UserAdminExportedFailed;
use Modules\User\App\Repositories\UserAdminRepository;

class UserAdminExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param string $userId
     * @param string $type
     * @return void
     */
    public function __construct(
        public string $userId,
        public string $type = "csv",
    ) {
        $this->onQueue("user-admin-queue");
    }

    /**
     * @param \Modules\User\App\Repositories\UserAdminRepository $userAdminRepository
     * @return void
     */
    public function handle(UserAdminRepository $userAdminRepository): void
    {
        try {
            $path = $userAdminRepository->exportToFile($this->type);
            $filename = basename($path);
            $relative = "exports/".$filename;

            if (! is_dir(storage_path("app/public/exports"))) {
                mkdir(storage_path("app/public/exports"), 0755, true);
            }

            copy($path, storage_path("app/public/".$relative));
            $fileUrl = Storage::disk("public")->url($relative);

            event(new UserAdminExported(
                userId: $this->userId,
                filename: $filename,
                fileUrl: $fileUrl,
                filePath: storage_path("app/public/".$relative),
                message: __("User export completed."),
            ));
        } catch (\Throwable $exception) {
            event(new UserAdminExportedFailed(
                userId: $this->userId,
                message: __("User export failed."),
                error: $exception->getMessage(),
            ));
        }
    }
}

<?php

namespace Modules\User\App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\User\App\Events\UserAdminExported;
use Modules\User\App\Events\UserAdminExportedFailed;
use Modules\User\App\Events\UserAdminImported;
use Modules\User\App\Events\UserAdminImportedFailed;
use Modules\User\App\Repositories\UserAdminRepository;

class UserAdminImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param string $userId
     * @param string $path
     * @param string $filename
     * @return void
     */
    public function __construct(
        public string $userId,
        public string $path,
        public string $filename,
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
            $result = $userAdminRepository->importFromFile($this->path);
            event(new UserAdminImported(
                userId: $this->userId,
                filename: $this->filename,
                totalImported: $result["imported"],
                totalSkipped: $result["skipped"],
                message: __("user.import.completed", [
                    "totalImported" => $result["imported"],
                    "totalSkipped" => $result["skipped"],
                ]),
            ));
        } catch (\Throwable $exception) {
            event(new UserAdminImportedFailed(
                userId: $this->userId,
                filename: $this->filename,
                message: __("user.import.failed"),
                error: $exception->getMessage(),
            ));
        }
    }
}

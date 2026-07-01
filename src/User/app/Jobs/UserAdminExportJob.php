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
     * @param array<string, mixed> $filters
     * @return void
     */
    public function __construct(
        public string $userId,
        public string $type = "csv",
        public array $filters = [],
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
            $path = $userAdminRepository->exportToFile($this->type, $this->filters);
            $filename = basename($path);
            $relative = "exports/".$filename;
            $fileUrl = Storage::disk("public")->url($relative);

            event(new UserAdminExported(
                userId: $this->userId,
                filename: $filename,
                fileUrl: $fileUrl,
                filePath: $path,
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

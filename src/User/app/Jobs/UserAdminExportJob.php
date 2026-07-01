<?php

namespace Modules\User\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
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
     * @param array<int, array{field: string, direction: string}> $orders
     * @return void
     */
    public function __construct(
        public string $userId,
        public string $type = "csv",
        public array $filters = [],
        public array $orders = [],
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
            $path = $userAdminRepository->exportToFile($this->type, $this->filters, $this->orders);
            $filename = basename($path);
            $fileUrl = "storage/export/".$filename;

            event(new UserAdminExported(
                userId: $this->userId,
                filename: $filename,
                fileUrl: $fileUrl,
                filePath: $path,
                message: __("user.export.completed"),
            ));
        } catch (\Throwable $exception) {
            event(new UserAdminExportedFailed(
                userId: $this->userId,
                message: __("user.export.failed"),
                error: $exception->getMessage(),
            ));
        }
    }
}

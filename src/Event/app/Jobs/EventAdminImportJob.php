<?php

namespace Modules\Event\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Event\App\Events\EventAdminImported;
use Modules\Event\App\Events\EventAdminImportedFailed;
use Modules\Event\App\Repositories\EventAdminRepository;

class EventAdminImportJob implements ShouldQueue
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
        $this->onQueue("event-admin-queue");
    }

    /**
     * @param EventAdminRepository $eventAdminRepository
     * @return void
     */
    public function handle(EventAdminRepository $eventAdminRepository): void
    {
        try {
            $result = $eventAdminRepository->importFromFile($this->path);

            event(new EventAdminImported(
                userId: $this->userId,
                filename: $this->filename,
                totalImported: $result["imported"],
                totalSkipped: $result["skipped"],
                message: __("event.import.completed"),
            ));
        } catch (\Throwable $exception) {
            event(new EventAdminImportedFailed(
                userId: $this->userId,
                filename: $this->filename,
                message: __("event.import.failed"),
                error: $exception->getMessage(),
            ));
        }
    }
}

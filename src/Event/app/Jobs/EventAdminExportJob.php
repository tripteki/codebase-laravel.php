<?php

namespace Modules\Event\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Modules\Event\App\Events\EventAdminExported;
use Modules\Event\App\Events\EventAdminExportedFailed;
use Modules\Event\App\Repositories\EventAdminRepository;

class EventAdminExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param string $userId
     * @param string $type
     * @param array $filters
     * @return void
     */
    public function __construct(
        public string $userId,
        public string $type = "csv",
        public array $filters = [],
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
            $path = $eventAdminRepository->exportToFile($this->type, $this->filters);
            $filename = basename($path);
            $relative = "exports/".$filename;
            $fileUrl = Storage::disk("public")->url($relative);

            event(new EventAdminExported(
                userId: $this->userId,
                filename: $filename,
                fileUrl: $fileUrl,
                filePath: $path,
                message: __("event.export.completed"),
            ));
        } catch (\Throwable $exception) {
            event(new EventAdminExportedFailed(
                userId: $this->userId,
                message: __("event.export.failed"),
                error: $exception->getMessage(),
            ));
        }
    }
}

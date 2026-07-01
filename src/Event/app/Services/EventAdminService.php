<?php

namespace Modules\Event\App\Services;

use App\Dtos\OffsetPaginationDto;
use App\Services\Service as BaseService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Modules\Event\App\Dtos\EventIdentifierDto;
use Modules\Event\App\Dtos\EventOverviewStatsDto;
use Modules\Event\App\Dtos\EventTransformerDto;
use Modules\Event\App\Jobs\EventAdminExportJob;
use Modules\Event\App\Jobs\EventAdminImportJob;
use Modules\Event\App\Repositories\EventAdminRepository;

class EventAdminService extends BaseService
{
    /**
     * @param EventAdminRepository $eventAdminRepository
     * @return void
     */
    public function __construct(
        protected EventAdminRepository $eventAdminRepository,
    ) {}

    /**
     * @return OffsetPaginationDto
     */
    public function all(): OffsetPaginationDto
    {
        $paginator = $this->eventAdminRepository->all();

        return $this->toOffsetPagination(
            $paginator,
            fn ($event) => EventTransformerDto::fromEvent($event),
        );
    }

    /**
     * @param EventIdentifierDto $identifier
     * @return EventTransformerDto
     */
    public function get(EventIdentifierDto $identifier): EventTransformerDto
    {
        return EventTransformerDto::fromEvent($this->eventAdminRepository->get($identifier->id));
    }

    /**
     * @param array{
     *     id: string,
     *     payload: array<string, mixed>,
     *     files: array<string, UploadedFile|null>
     * } $data
     * @return EventTransformerDto
     */
    public function create(array $data): EventTransformerDto
    {
        return EventTransformerDto::fromEvent(
            $this->eventAdminRepository->create($data["id"], $data["payload"], $data["files"]),
        );
    }

    /**
     * @param string $id
     * @param array{
     *     payload: array<string, mixed>,
     *     files: array<string, UploadedFile|null>
     * } $data
     * @return EventTransformerDto
     */
    public function update(string $id, array $data): EventTransformerDto
    {
        return EventTransformerDto::fromEvent(
            $this->eventAdminRepository->update($id, $data["payload"], $data["files"]),
        );
    }

    /**
     * @param EventIdentifierDto $identifier
     * @return EventTransformerDto
     */
    public function delete(EventIdentifierDto $identifier): EventTransformerDto
    {
        return EventTransformerDto::fromEvent($this->eventAdminRepository->delete($identifier->id));
    }

    /**
     * @param string $path
     * @param string $filename
     * @return string
     */
    public function import(string $path, string $filename): string
    {
        EventAdminImportJob::dispatch(
            (string) Auth::id(),
            $path,
            $filename,
        );

        return __("event.import.started");
    }

    /**
     * @param string $type
     * @param array<string, mixed> $filters
     * @return string
     */
    public function export(string $type = "csv", array $filters = []): string
    {
        EventAdminExportJob::dispatch(
            (string) Auth::id(),
            $type,
            $filters,
        );

        return __("event.export.started");
    }

    /**
     * @return EventOverviewStatsDto
     */
    public function overview(): EventOverviewStatsDto
    {
        $stats = $this->eventAdminRepository->overview();

        return new EventOverviewStatsDto(
            labels: $stats["labels"],
            series: $stats["series"],
        );
    }
}

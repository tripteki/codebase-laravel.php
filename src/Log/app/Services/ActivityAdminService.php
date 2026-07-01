<?php

namespace Modules\Log\App\Services;

use App\Dtos\OffsetPaginationDto;
use App\Services\Service as BaseService;
use Modules\Log\App\Dtos\ActivityIdentifierDto;
use Modules\Log\App\Dtos\ActivityTransformerDto;
use Modules\Log\App\Repositories\ActivityAdminRepository;

class ActivityAdminService extends BaseService
{
    /**
     * @param ActivityAdminRepository $activityAdminRepository
     * @return void
     */
    public function __construct(
        protected ActivityAdminRepository $activityAdminRepository,
    ) {}

    /**
     * @return OffsetPaginationDto
     */
    public function all(): OffsetPaginationDto
    {
        $paginator = $this->activityAdminRepository->all();

        return $this->toOffsetPagination(
            $paginator,
            fn ($activity) => ActivityTransformerDto::fromActivity($activity),
        );
    }

    /**
     * @param ActivityIdentifierDto $identifier
     * @return ActivityTransformerDto
     */
    public function get(ActivityIdentifierDto $identifier): ActivityTransformerDto
    {
        return ActivityTransformerDto::fromActivity(
            $this->activityAdminRepository->get($identifier->id),
            true,
        );
    }
}

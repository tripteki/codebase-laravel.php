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
     * @param \Modules\Log\App\Repositories\ActivityAdminRepository $activityAdminRepository
     * @return void
     */
    public function __construct(
        protected ActivityAdminRepository $activityAdminRepository,
    ) {}

    /**
     * @return \App\Dtos\OffsetPaginationDto
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
     * @param \Modules\Log\App\Dtos\ActivityIdentifierDto $identifier
     * @return \Modules\Log\App\Dtos\ActivityTransformerDto
     */
    public function get(ActivityIdentifierDto $identifier): ActivityTransformerDto
    {
        return ActivityTransformerDto::fromActivity(
            $this->activityAdminRepository->get($identifier->id),
            true,
        );
    }
}

<?php

namespace Modules\Notification\App\Services;

use Modules\Notification\App\Dtos\NotificationTransformerDto;
use Modules\Notification\App\Dtos\NotificationValidatorDto;
use Modules\Notification\App\Repositories\NotificationAdminRepository;
use App\Services\Service as BaseService;
use App\Dtos\OffsetPaginationDto;

class NotificationAdminService extends BaseService
{
    /**
     * @var \Modules\Notification\App\Repositories\NotificationAdminRepository
     */
    protected NotificationAdminRepository $notificationAdminRepository;

    /**
     * @param \Modules\Notification\App\Repositories\NotificationAdminRepository $notificationAdminRepository
     * @return void
     */
    public function __construct(NotificationAdminRepository $notificationAdminRepository)
    {
        $this->notificationAdminRepository = $notificationAdminRepository;
    }

    /**
     * @return \App\Dtos\OffsetPaginationDto
     */
    public function all(): OffsetPaginationDto
    {
        $paginator = $this->notificationAdminRepository->all();

        return $this->toOffsetPagination(
            $paginator,
            fn ($notification) => NotificationTransformerDto::fromNotification($notification, true),
        );
    }

    /**
     * @param \Modules\Notification\App\Dtos\NotificationValidatorDto $notificationData
     * @return \Modules\Notification\App\Dtos\NotificationTransformerDto
     */
    public function get(NotificationValidatorDto $notificationData): NotificationTransformerDto
    {
        return NotificationTransformerDto::fromNotification(
            $this->notificationAdminRepository->get($notificationData->id),
            true
        );
    }

    /**
     * @param \Modules\Notification\App\Dtos\NotificationValidatorDto $notificationData
     * @return \Modules\Notification\App\Dtos\NotificationTransformerDto
     */
    public function delete(NotificationValidatorDto $notificationData): NotificationTransformerDto
    {
        return NotificationTransformerDto::fromNotification(
            $this->notificationAdminRepository->delete($notificationData->id),
            true
        );
    }

    /**
     * @param \Modules\Notification\App\Dtos\NotificationValidatorDto $notificationData
     * @return \Modules\Notification\App\Dtos\NotificationTransformerDto
     */
    public function restore(NotificationValidatorDto $notificationData): NotificationTransformerDto
    {
        return NotificationTransformerDto::fromNotification(
            $this->notificationAdminRepository->restore($notificationData->id),
            true
        );
    }
}

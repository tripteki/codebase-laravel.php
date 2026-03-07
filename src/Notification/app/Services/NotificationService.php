<?php

namespace Modules\Notification\App\Services;

use Modules\Notification\App\Dtos\NotificationBatchPayloadDto;
use Modules\Notification\App\Dtos\NotificationCountTransformerDto;
use Modules\Notification\App\Dtos\NotificationUnreadTransformerDto;
use Modules\Notification\App\Dtos\NotificationValidatorDto;
use Modules\Notification\App\Dtos\UnreadNotificationValidatorDto;
use Modules\Notification\App\Dtos\NotificationTransformerDto;
use Modules\Notification\App\Repositories\NotificationRepository;
use App\Services\Service as BaseService;
use Spatie\LaravelData\PaginatedDataCollection;

class NotificationService extends BaseService
{
    /**
     * @var \Modules\Notification\App\Repositories\NotificationRepository
     */
    protected $notificationRepository;

    /**
     * @param \Modules\Notification\App\Repositories\NotificationRepository $notificationRepository
     * @return void
     */
    public function __construct(NotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * @return \Spatie\LaravelData\PaginatedDataCollection
     */
    public function all(): PaginatedDataCollection
    {
        $paginator = $this->notificationRepository->all();
        $paginator->setCollection(
            $paginator->getCollection()->map(fn ($notification) => NotificationTransformerDto::fromNotification($notification))
        );

        return NotificationTransformerDto::collect($paginator, PaginatedDataCollection::class);
    }

    /**
     * @return \Modules\Notification\App\Dtos\NotificationBatchPayloadDto
     */
    public function readall(): NotificationBatchPayloadDto
    {
        return NotificationBatchPayloadDto::from([
            "count" => $this->notificationRepository->readall(),
        ]);
    }

    /**
     * @param \Modules\Notification\App\Dtos\UnreadNotificationValidatorDto $notificationData
     * @return \Modules\Notification\App\Dtos\NotificationTransformerDto
     */
    public function read(UnreadNotificationValidatorDto $notificationData): NotificationTransformerDto
    {
        return NotificationTransformerDto::fromNotification(
            $this->notificationRepository->read($notificationData->id)
        );
    }

    /**
     * @return \Modules\Notification\App\Dtos\NotificationCountTransformerDto
     */
    public function count(): NotificationCountTransformerDto
    {
        return NotificationCountTransformerDto::from([
            "count" => $this->notificationRepository->count(),
        ]);
    }

    /**
     * @return \Modules\Notification\App\Dtos\NotificationUnreadTransformerDto
     */
    public function unread(): NotificationUnreadTransformerDto
    {
        return NotificationUnreadTransformerDto::from([
            "unread" => $this->notificationRepository->unread(),
        ]);
    }

    /**
     * @param \Modules\Notification\App\Dtos\NotificationValidatorDto $notificationData
     * @return \Modules\Notification\App\Dtos\NotificationTransformerDto
     */
    public function get(NotificationValidatorDto $notificationData): NotificationTransformerDto
    {
        return NotificationTransformerDto::fromNotification(
            $this->notificationRepository->get($notificationData->id)
        );
    }

    /**
     * @param \Modules\Notification\App\Dtos\NotificationValidatorDto $notificationData
     * @return \Modules\Notification\App\Dtos\NotificationTransformerDto
     */
    public function delete(NotificationValidatorDto $notificationData): NotificationTransformerDto
    {
        return NotificationTransformerDto::fromNotification(
            $this->notificationRepository->delete($notificationData->id)
        );
    }
}

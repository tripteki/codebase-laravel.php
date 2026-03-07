<?php

namespace Modules\Notification\App\Repositories;

use Modules\Notification\App\Models\Notification;
use App\Repositories\Repository as BaseRepository;

class NotificationRepository extends BaseRepository
{
    /**
     * @return \Modules\Notification\App\Models\Notification[]
     */
    public function all(): \Illuminate\Pagination\LengthAwarePaginator
    {
        $model = $this->getUser()->notifications();

        return parent::accessAll(
            fn () => $model,
            sortables: [ "id", "type", "updated_at", "read_at", ],
            defaultSorts: [ "-updated_at", "-read_at", ],
            filterables: [ "id", "type", "updated_at", "read_at", ],
            defaultFilters: [],
        );
    }

    /**
     * @return int
     */
    public function readall(): int
    {
        $count = $this->getUser()->unreadNotifications()->count();

        parent::mutateUpdate(function (): void {
            $this->getUser()->unreadNotifications()->update([ "read_at" => now(), ]);
        });

        return $count;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return $this->getUser()->notifications()->count();
    }

    /**
     * @param int|string $id
     * @return \Modules\Notification\App\Models\Notification|null
     */
    public function read(int|string $id): ?Notification
    {
        $model = $this->getUser()->unreadNotifications()->findOrFail($id);

        return parent::mutateUpdate(
            fn () => $model->markAsRead() ? null : $model
        );
    }

    /**
     * @return int
     */
    public function unread(): int
    {
        return $this->getUser()->unreadNotifications()->count();
    }

    /**
     * @param int|string $id
     * @return \Modules\Notification\App\Models\Notification|null
     */
    public function get(int|string $id): ?Notification
    {
        $model = $this->getUser()->notifications()->findOrFail($id);

        return parent::accessGet(
            fn () => $model ?? null
        );
    }

    /**
     * @param int|string $id
     * @return \Modules\Notification\App\Models\Notification|null
     */
    public function delete(int|string $id): ?Notification
    {
        $model = $this->getUser()->notifications()->findOrFail($id);

        return parent::mutateDelete(
            fn () => $model->delete() ? $model : null
        );
    }
}

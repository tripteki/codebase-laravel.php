<?php

namespace Modules\Notification\App\Repositories;

use App\Repositories\Repository as BaseRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Notification\App\Models\Notification;
use Spatie\QueryBuilder\AllowedFilter;

class NotificationRepository extends BaseRepository
{
    /**
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function all(): LengthAwarePaginator
    {
        $model = $this->getUser()->notifications();

        return parent::accessAll(
            fn () => $model,
            sortables: [ "id", "type", "updated_at", "read_at", ],
            defaultSorts: [ "-updated_at", "-read_at", ],
            filterables: [
                AllowedFilter::partial("type"),
                AllowedFilter::exact("id"),
                AllowedFilter::callback("status", function ($query, $value): void {
                    if ((string) $value === "unread") {
                        $query->whereNull("read_at");
                    }

                    if ((string) $value === "read") {
                        $query->whereNotNull("read_at");
                    }
                }),
            ],
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
            fn () => $model->markAsRead() ? null : $model,
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
            fn () => $model ?? null,
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
            fn () => $model->delete() ? $model : null,
        );
    }
}

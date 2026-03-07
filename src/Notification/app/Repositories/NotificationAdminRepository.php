<?php

namespace Modules\Notification\App\Repositories;

use Modules\Notification\App\Models\Notification;
use App\Repositories\Repository as BaseRepository;

class NotificationAdminRepository extends BaseRepository
{
    /**
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function all(): \Illuminate\Pagination\LengthAwarePaginator
    {
        $model = Notification::query()->with("notifiable");

        return parent::accessAll(
            fn () => $model,
            sortables: [ "id", "type", "updated_at", "read_at", "created_at", ],
            defaultSorts: [ "-updated_at", "-read_at", ],
            filterables: [ "id", "type", "updated_at", "read_at", "created_at", ],
            defaultFilters: [],
        );
    }

    /**
     * @param string $id
     * @return \Modules\Notification\App\Models\Notification|null
     */
    public function get(string $id): ?Notification
    {
        return parent::accessGet(
            fn () => Notification::with("notifiable")->findOrFail($id)
        );
    }

    /**
     * @param string $id
     * @return \Modules\Notification\App\Models\Notification|null
     */
    public function delete(string $id): ?Notification
    {
        return parent::mutateDelete(
            function () use ($id): Notification {
                $notification = Notification::findOrFail($id);
                $notification->delete();

                return $notification;
            }
        );
    }

    /**
     * @param string $id
     * @return \Modules\Notification\App\Models\Notification|null
     */
    public function restore(string $id): ?Notification
    {
        return parent::mutateUpdate(
            function () use ($id): Notification {
                $notification = Notification::withTrashed()->findOrFail($id);
                $notification->restore();

                return $notification->fresh(["notifiable"]);
            }
        );
    }
}

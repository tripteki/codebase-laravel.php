<?php

namespace Modules\Notification\App\Policies;

use App\Models\User;
use Modules\Acl\App\Enums\GuardEnum;
use Modules\Notification\App\Enums\PermissionEnum;
use Modules\Notification\App\Models\Notification;

class NotificationPolicy
{
    /**
     * @param \App\Models\User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $this->allows($user, PermissionEnum::NOTIFICATION_VIEW);
    }

    /**
     * @param \App\Models\User $user
     * @param \Modules\Notification\App\Models\Notification $notification
     * @return bool
     */
    public function view(User $user, Notification $notification): bool
    {
        return $this->allows($user, PermissionEnum::NOTIFICATION_VIEW);
    }

    /**
     * @param \App\Models\User $user
     * @param \Modules\Notification\App\Models\Notification $notification
     * @return bool
     */
    public function delete(User $user, Notification $notification): bool
    {
        return $this->allows($user, PermissionEnum::NOTIFICATION_DELETE);
    }

    /**
     * @param \App\Models\User $user
     * @param \Modules\Notification\App\Models\Notification $notification
     * @return bool
     */
    public function restore(User $user, Notification $notification): bool
    {
        return $this->allows($user, PermissionEnum::NOTIFICATION_RESTORE);
    }

    /**
     * @param \App\Models\User $user
     * @param \Modules\Notification\App\Enums\PermissionEnum $permission
     * @return bool
     */
    protected function allows(User $user, PermissionEnum $permission): bool
    {
        return $user->hasPermissionTo($permission->value, GuardEnum::WEB->value);
    }
}

<?php

namespace Modules\Log\App\Policies;

use App\Models\User;
use Modules\Acl\App\Enums\GuardEnum;
use Modules\Log\App\Enums\PermissionEnum;
use Modules\Log\App\Models\Activity;

class ActivityPolicy
{
    /**
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $this->allows($user, PermissionEnum::ACTIVITY_VIEW);
    }

    /**
     * @param User $user
     * @param Activity $activity
     * @return bool
     */
    public function view(User $user, Activity $activity): bool
    {
        return $this->allows($user, PermissionEnum::ACTIVITY_VIEW);
    }

    /**
     * @param User $user
     * @param PermissionEnum $permission
     * @return bool
     */
    protected function allows(User $user, PermissionEnum $permission): bool
    {
        return $user->hasPermissionTo($permission->value, GuardEnum::WEB->value);
    }
}

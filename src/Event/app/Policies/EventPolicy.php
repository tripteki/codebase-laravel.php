<?php

namespace Modules\Event\App\Policies;

use App\Models\User;
use Modules\Acl\App\Enums\GuardEnum;
use Modules\Event\App\Enums\PermissionEnum;
use Modules\Event\App\Models\Event;

class EventPolicy
{
    /**
     * @param \App\Models\User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $this->allows($user, PermissionEnum::EVENT_VIEW);
    }

    /**
     * @param \App\Models\User $user
     * @param \Modules\Event\App\Models\Event $event
     * @return bool
     */
    public function view(User $user, Event $event): bool
    {
        return $this->allows($user, PermissionEnum::EVENT_VIEW);
    }

    /**
     * @param \App\Models\User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $this->allows($user, PermissionEnum::EVENT_CREATE);
    }

    /**
     * @param \App\Models\User $user
     * @param \Modules\Event\App\Models\Event $event
     * @return bool
     */
    public function update(User $user, Event $event): bool
    {
        return $this->allows($user, PermissionEnum::EVENT_UPDATE);
    }

    /**
     * @param \App\Models\User $user
     * @param \Modules\Event\App\Models\Event $event
     * @return bool
     */
    public function delete(User $user, Event $event): bool
    {
        return $this->allows($user, PermissionEnum::EVENT_DELETE);
    }

    /**
     * @param \App\Models\User $user
     * @return bool
     */
    public function import(User $user): bool
    {
        return $this->allows($user, PermissionEnum::EVENT_IMPORT);
    }

    /**
     * @param \App\Models\User $user
     * @return bool
     */
    public function export(User $user): bool
    {
        return $this->allows($user, PermissionEnum::EVENT_EXPORT);
    }

    /**
     * @param \App\Models\User $user
     * @param \Modules\Event\App\Enums\PermissionEnum $permission
     * @return bool
     */
    protected function allows(User $user, PermissionEnum $permission): bool
    {
        return $user->hasPermissionTo($permission->value, GuardEnum::WEB->value);
    }
}

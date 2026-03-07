<?php

namespace Modules\User\App\Policies;

use App\Models\User;
use Modules\Acl\App\Enums\GuardEnum;
use Modules\User\App\Enums\PermissionEnum;

class UserPolicy
{
    /**
     * @param \App\Models\User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $this->allows($user, PermissionEnum::USER_VIEW);
    }

    /**
     * @param \App\Models\User $user
     * @param \App\Models\User $model
     * @return bool
     */
    public function view(User $user, User $model): bool
    {
        if ($user->id === $model->id) {
            return true;
        }

        return $this->allows($user, PermissionEnum::USER_VIEW);
    }

    /**
     * @param \App\Models\User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $this->allows($user, PermissionEnum::USER_CREATE);
    }

    /**
     * @param \App\Models\User $user
     * @param \App\Models\User $model
     * @return bool
     */
    public function update(User $user, User $model): bool
    {
        return $this->allows($user, PermissionEnum::USER_UPDATE);
    }

    /**
     * @param \App\Models\User $user
     * @param \App\Models\User $model
     * @return bool
     */
    public function delete(User $user, User $model): bool
    {
        return $this->allows($user, PermissionEnum::USER_DELETE);
    }

    /**
     * @param \App\Models\User $user
     * @param \App\Models\User $model
     * @return bool
     */
    public function restore(User $user, User $model): bool
    {
        return $this->allows($user, PermissionEnum::USER_RESTORE);
    }

    /**
     * @param \App\Models\User $user
     * @param \App\Models\User $model
     * @return bool
     */
    public function verify(User $user, User $model): bool
    {
        return $this->allows($user, PermissionEnum::USER_UPDATE);
    }

    /**
     * @param \App\Models\User $user
     * @return bool
     */
    public function import(User $user): bool
    {
        return $this->allows($user, PermissionEnum::USER_IMPORT);
    }

    /**
     * @param \App\Models\User $user
     * @return bool
     */
    public function export(User $user): bool
    {
        return $this->allows($user, PermissionEnum::USER_EXPORT);
    }

    /**
     * @param \App\Models\User $user
     * @param \Modules\User\App\Enums\PermissionEnum $permission
     * @return bool
     */
    protected function allows(User $user, PermissionEnum $permission): bool
    {
        return $user->hasPermissionTo($permission->value, GuardEnum::WEB->value);
    }
}

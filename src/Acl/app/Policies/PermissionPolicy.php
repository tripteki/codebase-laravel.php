<?php

namespace Modules\Acl\App\Policies;

use App\Models\User;
use Modules\Acl\App\Enums\GuardEnum;
use Modules\Acl\App\Enums\PermissionEnum;
use Modules\Acl\App\Enums\PermissionEnum as AclPermissionEnum;
use Modules\Acl\App\Models\Permission;

class PermissionPolicy
{
    /**
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $this->allows($user, AclPermissionEnum::PERMISSION_VIEW);
    }

    /**
     * @param User $user
     * @param Permission $model
     * @return bool
     */
    public function view(User $user, Permission $model): bool
    {
        return $this->allows($user, AclPermissionEnum::PERMISSION_VIEW);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $this->allows($user, AclPermissionEnum::PERMISSION_CREATE);
    }

    /**
     * @param User $user
     * @param Permission $model
     * @return bool
     */
    public function update(User $user, Permission $model): bool
    {
        return $this->allows($user, AclPermissionEnum::PERMISSION_UPDATE);
    }

    /**
     * @param User $user
     * @param Permission $model
     * @return bool
     */
    public function delete(User $user, Permission $model): bool
    {
        return $this->allows($user, AclPermissionEnum::PERMISSION_DELETE);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function import(User $user): bool
    {
        return $this->allows($user, AclPermissionEnum::PERMISSION_IMPORT);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function export(User $user): bool
    {
        return $this->allows($user, AclPermissionEnum::PERMISSION_EXPORT);
    }

    /**
     * @param User $user
     * @param PermissionEnum $permission
     * @return bool
     */
    protected function allows(User $user, AclPermissionEnum $permission): bool
    {
        return $user->hasPermissionTo($permission->value, GuardEnum::WEB->value);
    }
}

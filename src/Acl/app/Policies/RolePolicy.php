<?php

namespace Modules\Acl\App\Policies;

use App\Models\User;
use Modules\Acl\App\Enums\GuardEnum;
use Modules\Acl\App\Enums\PermissionEnum;
use Modules\Acl\App\Enums\PermissionEnum as AclPermissionEnum;
use Modules\Acl\App\Models\Role;

class RolePolicy
{
    /**
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $this->allows($user, AclPermissionEnum::ROLE_VIEW);
    }

    /**
     * @param User $user
     * @param Role $model
     * @return bool
     */
    public function view(User $user, Role $model): bool
    {
        return $this->allows($user, AclPermissionEnum::ROLE_VIEW);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $this->allows($user, AclPermissionEnum::ROLE_CREATE);
    }

    /**
     * @param User $user
     * @param Role $model
     * @return bool
     */
    public function update(User $user, Role $model): bool
    {
        return $this->allows($user, AclPermissionEnum::ROLE_UPDATE);
    }

    /**
     * @param User $user
     * @param Role $model
     * @return bool
     */
    public function delete(User $user, Role $model): bool
    {
        return $this->allows($user, AclPermissionEnum::ROLE_DELETE);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function import(User $user): bool
    {
        return $this->allows($user, AclPermissionEnum::ROLE_IMPORT);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function export(User $user): bool
    {
        return $this->allows($user, AclPermissionEnum::ROLE_EXPORT);
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

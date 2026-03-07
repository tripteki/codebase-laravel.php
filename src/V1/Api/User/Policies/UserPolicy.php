<?php

namespace Src\V1\Api\User\Policies;

use App\Models\User;
use Src\V1\Api\User\Enums\PermissionEnum;

class UserPolicy
{
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

        return $user->can(PermissionEnum::USER_VIEW->value);
    }

    /**
     * @param \App\Models\User $user
     * @param \App\Models\User $model
     * @return bool
     */
    public function update(User $user, User $model): bool
    {
        return $user->can(PermissionEnum::USER_UPDATE->value);
    }

    /**
     * @param \App\Models\User $user
     * @param \App\Models\User $model
     * @return bool
     */
    public function delete(User $user, User $model): bool
    {
        return $user->can(PermissionEnum::USER_DELETE->value);
    }
}

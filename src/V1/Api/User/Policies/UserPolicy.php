<?php

namespace Src\V1\Api\User\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * @param \App\Models\User $user
     * @return bool
     */
    public function update(User $user): bool
    {
        return true;
    }
}

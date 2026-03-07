<?php

namespace Modules\User\App\Repositories;

use App\Models\User;
use App\Repositories\Repository as BaseRepository;

class UserRepository extends BaseRepository
{
    /**
     * @return \App\Models\User|null
     */
    public function get(): ?User
    {
        $model = $this->getUser();

        return parent::accessGet(
            fn () => $model ?? null
        );
    }

    /**
     * @param array $userData
     * @return \App\Models\User|null
     */
    public function create(array $userData): ?User
    {
        return parent::mutateCreate(
            fn () => User::create($userData) ?? null
        );
    }
}

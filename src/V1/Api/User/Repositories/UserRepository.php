<?php

namespace Src\V1\Api\User\Repositories;

use App\Models\User;
use Src\V1\Api\Common\Repositories\Repository as BaseRepository;

class UserRepository extends BaseRepository
{
    /**
     * @return \App\Models\User[]
     */
    public function all(): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = User::where("id", $this->getUser()->getKey());

        return parent::accessAll(
            fn () => $query,
            sortables: [ "id", "name", "email", "created_at", "updated_at", "deleted_at", ],
            filterables: [ "id", "name", "email", "created_at", "updated_at", "deleted_at", ]
        );
    }

    /**
     * @return \App\Models\User
     */
    public function get(): User
    {
        $query = User::where("id", $this->getUser()->getKey());

        return parent::accessGet(
            fn () => $query->firstOrFail()
        );
    }

    /**
     * @param \App\Models\User $userData
     * @return \App\Models\User
     */
    public function update(array $userData): User
    {
        $model = $this->get();

        return parent::mutateUpdate(
            fn () => $model->update($userData) ? $model : null
        );
    }

    /**
     * @param \App\Models\User $userData
     * @return \App\Models\User
     */
    public function create(array $userData): User
    {
        return parent::mutateCreate(
            fn () => User::create($userData) ?? null
        );
    }

    /**
     * @return \App\Models\User
     */
    public function restore(): User
    {
        $model = User::deactivated()->where("id", $this->getUser()->getKey())->firstOrFail();

        return parent::mutateDelete(
            fn () => $model->restore() ? $model : null
        );
    }

    /**
     * @return \App\Models\User
     */
    public function delete(): User
    {
        $model = $this->get();

        return parent::mutateDelete(
            fn () => $model->delete() ? $model : null
        );
    }
}

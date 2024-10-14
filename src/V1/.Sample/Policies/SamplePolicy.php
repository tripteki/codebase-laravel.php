<?php

namespace Src\V1\Sample\Policies;

use App\Models\User;
use Src\V1\Sample\Models\SampleModel;
use Illuminate\Auth\Access\Response;

class SamplePolicy
{
    /**
     * @param \App\Models\User $user
     * @return bool
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * @param \App\Models\User $user
     * @param \Src\V1\Sample\Models\SampleModel $sample
     * @return bool
     */
    public function view(User $user, SampleModel $sample)
    {
        return $user?->id === $sample->user_id;
    }

    /**
     * @param \App\Models\User $user
     * @param \Src\V1\Sample\Models\SampleModel $sample
     * @return bool
     */
    public function update(User $user, SampleModel $sample)
    {
        return $user?->id === $sample->user_id;
    }

    /**
     * @param \App\Models\User $user
     * @param \Src\V1\Sample\Models\SampleModel $sample
     * @return bool
     */
    public function delete(User $user, SampleModel $sample)
    {
        return $user?->id === $sample->user_id;
    }
}

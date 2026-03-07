<?php

namespace App\Observers;

use App\Models\User;
use Src\V1\Api\Log\Models\Activity;

class ActivityObserver
{
    /**
     * @param \Src\V1\Api\Log\Models\Activity $activity
     * @return void
     */
    public function creating(Activity $activity): void
    {
        if (! config("tenancy.is_tenancy")) {
            return;
        }

        if ($activity->tenant_id !== null) {
            return;
        }

        if ($activity->causer_type === User::class && $activity->causer_id) {
            $user = User::find($activity->causer_id);
            if ($user && $user->tenant_id) {
                $activity->tenant_id = $user->tenant_id;

                return;
            }
        }

        if (tenancy()->initialized) {
            $activity->tenant_id = tenant("id");
        }
    }
}

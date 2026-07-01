<?php

namespace Modules\Log\App\Concerns;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

trait LogsAclModelActivity
{
    use LogsActivity;

    /**
     * @return \Spatie\Activitylog\LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([ "name", "guard_name", ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}

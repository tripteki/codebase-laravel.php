<?php

namespace Modules\Log\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity as SpatieActivity;

class Activity extends SpatieActivity
{
    use HasUlids;

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var string
     */
    protected $keyType = 'string';

    /**
     * @return void
     */
    protected static function booted(): void
    {
        static::creating(function (Activity $activity): void {
            if ($activity->tenant_id !== null && $activity->tenant_id !== "") {
                return;
            }

            $tenantId = current_tenant_id();

            if ($tenantId !== null) {
                $activity->tenant_id = $tenantId;

                return;
            }

            $causer = $activity->causer;

            if ($causer instanceof User && $causer->tenant_id !== null) {
                $activity->tenant_id = $causer->tenant_id;

                return;
            }

            $subject = $activity->subject;

            if ($subject instanceof Model && isset($subject->tenant_id)) {
                $tenantId = $subject->tenant_id;

                if (is_string($tenantId) && $tenantId !== "") {
                    $activity->tenant_id = $tenantId;
                }
            }
        });
    }
}

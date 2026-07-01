<?php

namespace Modules\Acl\App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Modules\Log\App\Concerns\LogsAclModelActivity;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    use HasUlids,
        LogsAclModelActivity;

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var string
     */
    protected $keyType = 'string';
}

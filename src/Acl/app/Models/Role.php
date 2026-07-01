<?php

namespace Modules\Acl\App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Modules\Log\App\Concerns\LogsAclModelActivity;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
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

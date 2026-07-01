<?php

namespace Modules\Event\App\Models;

use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Event extends BaseTenant
{
    use HasDomains;

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var string
     */
    protected $keyType = "string";

    /**
     * @var string
     */
    protected $table = "tenants";

    /**
     * @return array<int, string>
     */
    public static function getCustomColumns(): array
    {
        return [
            "id",
            "created_at",
            "updated_at",
        ];
    }
}

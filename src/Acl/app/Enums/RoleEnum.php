<?php

namespace Modules\Acl\App\Enums;

/**
 * @enum \Modules\Acl\App\Enums\RoleEnum
 */
enum RoleEnum: string
{
    /**
     * @var string
     */
    case SUPERADMIN = "superadmin";

    /**
     * @var string
     */
    case ADMIN = "admin";

    /**
     * @var string
     */
    case USER = "user";

    /**
     * @return list<self>
     */
    public static function tenantBootstrapRoles(): array
    {
        return [
            self::ADMIN,
            self::USER,
        ];
    }
}

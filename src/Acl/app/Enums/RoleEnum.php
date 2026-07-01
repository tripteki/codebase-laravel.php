<?php

namespace Modules\Acl\App\Enums;

/**
 * @enum RoleEnum
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
     * @var string
     */
    case GUEST = "guest";

    /**
     * @return list<self>
     */
    public static function tenantBootstrapRoles(): array
    {
        return [
            self::ADMIN,
            self::USER,
            self::GUEST,
        ];
    }
}

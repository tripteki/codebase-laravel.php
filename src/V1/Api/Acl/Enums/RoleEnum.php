<?php

namespace Src\V1\Api\Acl\Enums;

/**
 * @enum RoleEnum
 */
enum RoleEnum: string
{
    case SUPERADMIN = "superadmin";
    case ADMIN = "admin";
    case USER = "user";
    case GUEST = "guest";
}

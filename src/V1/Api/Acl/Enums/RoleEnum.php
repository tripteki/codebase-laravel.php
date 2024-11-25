<?php

namespace Src\V1\Api\Acl\Enums;

/**
 * @enum RoleEnum
 */
enum RoleEnum: string
{
    case SUPERADMIN = "superadmin";
    case ADMINISTRATOR = "admin";
}

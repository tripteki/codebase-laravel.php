<?php

namespace Src\V1\Api\User\Enums;

/**
 * @enum RoleEnum
 */
enum RoleEnum: string
{
    case SUPERADMIN = "superadmin";
    case ADMINISTRATOR = "admin";
}

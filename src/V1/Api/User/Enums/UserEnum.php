<?php

namespace Src\V1\Api\User\Enums;

/**
 * @enum UserEnum
 */
enum UserEnum: string
{
    /**
     * @var string
     */
    case SUPERUSER = "superuser";

    /**
     * @var string
     */
    case ADMIN = "admin";
}

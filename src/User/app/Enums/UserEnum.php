<?php

namespace Modules\User\App\Enums;

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

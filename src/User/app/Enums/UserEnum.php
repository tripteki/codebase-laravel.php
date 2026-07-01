<?php

namespace Modules\User\App\Enums;

/**
 * @enum \Modules\User\App\Enums\UserEnum
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

<?php

namespace Modules\Acl\App\Enums;

/**
 * @enum GuardEnum
 */
enum GuardEnum: string
{
    /**
     * @var string
     */
    case WEB = "web";

    /**
     * @var string
     */
    case API = "api";
}

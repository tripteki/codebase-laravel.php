<?php

namespace Src\V1\Api\Acl\Enums;

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

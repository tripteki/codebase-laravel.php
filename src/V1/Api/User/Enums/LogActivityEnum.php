<?php

namespace Src\V1\Api\User\Enums;

/**
 * @enum LogActivityEnum
 */
enum LogActivityEnum: string
{
    /**
     * @var string
     */
    case CREATED = "created";

    /**
     * @var string
     */
    case UPDATED = "updated";

    /**
     * @var string
     */
    case DELETED = "deleted";

    /**
     * @var string
     */
    case RESTORED = "restored";
}

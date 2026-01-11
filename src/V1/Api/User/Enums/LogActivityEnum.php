<?php

namespace Src\V1\Api\User\Enums;

/**
 * @enum LogActivityEnum
 */
enum LogActivityEnum: string
{
    case CREATED = "created";
    case UPDATED = "updated";
    case DELETED = "deleted";
    case RESTORED = "restored";
}

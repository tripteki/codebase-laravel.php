<?php

namespace Src\V1\Api\User\Enums;

/**
 * @enum PermissionEnum
 */
enum PermissionEnum: string
{
    /**
     * User Resource Permissions
     */
    case USER_VIEW = "user.view";
    case USER_CREATE = "user.create";
    case USER_UPDATE = "user.update";
    case USER_DELETE = "user.delete";
    case USER_RESTORE = "user.restore";
    case USER_FORCE_DELETE = "user.force-delete";
    case USER_IMPORT = "user.import";
    case USER_EXPORT = "user.export";
}

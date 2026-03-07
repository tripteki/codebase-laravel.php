<?php

namespace Src\V1\Api\User\Enums;

/**
 * @enum PermissionEnum
 */
enum PermissionEnum: string
{
    /**
     * @var string
     */
    case USER_VIEW = "user.view";

    /**
     * @var string
     */
    case USER_CREATE = "user.create";

    /**
     * @var string
     */
    case USER_UPDATE = "user.update";

    /**
     * @var string
     */
    case USER_DELETE = "user.delete";

    /**
     * @var string
     */
    case USER_RESTORE = "user.restore";

    /**
     * @var string
     */
    case USER_IMPORT = "user.import";

    /**
     * @var string
     */
    case USER_EXPORT = "user.export";
}

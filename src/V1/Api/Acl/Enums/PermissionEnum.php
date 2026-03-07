<?php

namespace Src\V1\Api\Acl\Enums;

/**
 * @enum PermissionEnum
 */
enum PermissionEnum: string
{
    /**
     * @var string
     */
    case ROLE_VIEW = "role.view";

    /**
     * @var string
     */
    case ROLE_CREATE = "role.create";

    /**
     * @var string
     */
    case ROLE_UPDATE = "role.update";

    /**
     * @var string
     */
    case ROLE_DELETE = "role.delete";

    /**
     * @var string
     */
    case ROLE_RESTORE = "role.restore";

    /**
     * @var string
     */
    case ROLE_IMPORT = "role.import";

    /**
     * @var string
     */
    case ROLE_EXPORT = "role.export";

    /**
     * @var string
     */
    case PERMISSION_VIEW = "permission.view";

    /**
     * @var string
     */
    case PERMISSION_CREATE = "permission.create";

    /**
     * @var string
     */
    case PERMISSION_UPDATE = "permission.update";

    /**
     * @var string
     */
    case PERMISSION_DELETE = "permission.delete";

    /**
     * @var string
     */
    case PERMISSION_RESTORE = "permission.restore";

    /**
     * @var string
     */
    case PERMISSION_IMPORT = "permission.import";

    /**
     * @var string
     */
    case PERMISSION_EXPORT = "permission.export";
}

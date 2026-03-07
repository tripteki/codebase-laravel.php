<?php

namespace Modules\Acl\App\Enums;

/**
 * @enum RoleEnum
 */
enum RoleEnum: string
{
    /**
     * @var string
     */
    case SUPERADMIN = "superadmin";

    /**
     * @var string
     */
    case ADMIN = "admin";

    /**
     * @var string
     */
    case SPEAKER = "speaker";

    /**
     * @var string
     */
    case EXHIBITOR = "exhibitor";

    /**
     * @var string
     */
    case SPONSOR = "sponsor";

    /**
     * @var string
     */
    case DELEGATE = "delegate";

    /**
     * @var string
     */
    case VISITOR = "visitor";
}

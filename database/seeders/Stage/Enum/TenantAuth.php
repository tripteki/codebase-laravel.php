<?php

namespace Database\Seeders\Stage\Enum;

/**
 * @enum TenantAuth
 */
enum TenantAuth: string
{
    /**
     * @var string
     */
    case BRAND_DESCRIPTION = "brand_description";

    /**
     * @var string
     */
    case EVENT_LIVE = "event_live";

    /**
     * @var string
     */
    case INTERACTIVE_TECH = "interactive_tech";

    /**
     * @var string
     */
    case ECOSYSTEM = "ecosystem";

    /**
     * @var string
     */
    case WELCOME_BACK_MESSAGE = "welcome_back_message";

    /**
     * @return string
     */
    public static function contentGroup(): string
    {
        return "auth";
    }
}

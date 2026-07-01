<?php

namespace Modules\Auth\App\Enums;

/**
 * @enum \Modules\Auth\App\Enums\UserAuthTokenScopeEnum
 */
enum UserAuthTokenScopeEnum: string
{
    /**
     * @var string
     */
    case Access = "ACCESS_TOKEN";

    /**
     * @var string
     */
    case Refresh = "REFRESH_TOKEN";
}

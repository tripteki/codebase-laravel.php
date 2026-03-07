<?php

namespace Modules\Auth\App\Enums;

enum UserAuthTokenScopeEnum: string
{
    case Access = "ACCESS_TOKEN";
    case Refresh = "REFRESH_TOKEN";
}

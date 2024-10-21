<?php

use Illuminate\Support\Facades\Route;

require __DIR__."/../src/V0/SettingProfile/Routes/user/setting/profile.php";
require __DIR__."/../src/V0/SettingProfile/Routes/admin/setting/profile.php";
require __DIR__."/../src/V0/Notification/Routes/user/notification.php";
require __DIR__."/../src/V0/Notification/Routes/admin/notification.php";
require __DIR__."/../src/V0/Log/Routes/user/log.php";
require __DIR__."/../src/V0/Log/Routes/admin/log.php";
require __DIR__."/../src/V0/ACL/Routes/user/acl.php";
require __DIR__."/../src/V0/ACL/Routes/admin/acl.php";
require __DIR__."/../src/V0/User/Routes/admin/user.php";
require __DIR__."/../src/V0/Auth/Routes/user/auth-api.php";

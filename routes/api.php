<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

require __DIR__."/auth-api.php";
require __DIR__."/user/log.php";
require __DIR__."/admin/log.php";
require __DIR__."/user/setting.php";
require __DIR__."/admin/setting.php";
require __DIR__."/user/setting/locale.php";
require __DIR__."/admin/setting/locale.php";
require __DIR__."/user/setting/profile.php";
require __DIR__."/admin/setting/profile.php";
require __DIR__."/user/setting/menu.php";
require __DIR__."/admin/setting/menu.php";
require __DIR__."/admin/user.php";
require __DIR__."/user/acl.php";
require __DIR__."/admin/acl.php";
require __DIR__."/user/notification.php";
require __DIR__."/admin/notification.php";
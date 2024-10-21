<?php

use Src\V0\Notification\Http\Controllers\Admin\Notification\NotificationAdminController;
use Illuminate\Support\Facades\Route;

Route::prefix(config("adminer.route.admin"))->middleware(config("adminer.middleware.admin"))->group(function () {

    /**
     * Notifications.
     */
    Route::apiResource("notifications", NotificationAdminController::class)->only([ "index", "show", ])->parameters([ "notifications" => "notification", ]);
    Route::get("notifications-export", [ NotificationAdminController::class, "export", ]);
});

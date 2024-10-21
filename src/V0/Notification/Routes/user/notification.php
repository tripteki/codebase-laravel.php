<?php

use Src\V0\Notification\Http\Controllers\Notification\NotificationController;
use Illuminate\Support\Facades\Route;

Route::prefix(config("adminer.route.user"))->middleware(config("adminer.middleware.user"))->group(function () {

    /**
     * Notifications.
     */
    Route::get("notifications", [ NotificationController::class, "index", ]);
    Route::put("notifications/{notification?}", [ NotificationController::class, "update", ]);
    Route::delete("notifications/{notification?}", [ NotificationController::class, "destroy", ]);
});

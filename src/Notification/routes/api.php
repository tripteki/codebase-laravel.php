<?php

use Modules\Notification\App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::middleware([ "auth:api", "jwt.scope:ACCESS_TOKEN", "verified", "throttle:api-read", ])->group(function (): void {

    Route::get("v1/notifications", [ NotificationController::class, "index", ]);
    Route::get("v1/notifications/count", [ NotificationController::class, "count", ]);
    Route::match([ "put", "patch", ], "v1/notifications/read-all", [ NotificationController::class, "readall", ]);
    Route::match([ "put", "patch", ], "v1/notifications/read/{id}", [ NotificationController::class, "read", ]);
    Route::get("v1/notifications/unread", [ NotificationController::class, "unread", ]);
    Route::get("v1/notifications/{id}", [ NotificationController::class, "show", ]);
    Route::delete("v1/notifications/{id}", [ NotificationController::class, "destroy", ]);
});

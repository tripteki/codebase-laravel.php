<?php

use App\Support\Throttle;
use Illuminate\Support\Facades\Route;
use Modules\Notification\App\Http\Controllers\NotificationController;

Route::middleware([
    "auth:api",
    "jwt.scope:ACCESS_TOKEN",
    "verified",
    ...Throttle::middleware("api-read"),
])->group(function (): void {
    Route::get("v1/notifications", [ NotificationController::class, "index", ]);
    Route::get("v1/notifications/count", [ NotificationController::class, "count", ]);
    Route::get("v1/notifications/unread", [ NotificationController::class, "unread", ]);
    Route::get("v1/notifications/{id}", [ NotificationController::class, "show", ]);
});

Route::middleware([
    "auth:api",
    "jwt.scope:ACCESS_TOKEN",
    "verified",
    ...Throttle::middleware("api-write"),
])->group(function (): void {
    Route::match([ "put", "patch", ], "v1/notifications/read-all", [ NotificationController::class, "readall", ]);
    Route::match([ "put", "patch", ], "v1/notifications/read/{id}", [ NotificationController::class, "read", ]);
    Route::delete("v1/notifications/{id}", [ NotificationController::class, "destroy", ]);
});

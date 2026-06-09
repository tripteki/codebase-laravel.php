<?php

use App\Support\Throttle;
use Illuminate\Support\Facades\Route;
use Modules\Notification\App\Http\Controllers\NotificationAdminController;

Route::middleware([
    "auth:api",
    "jwt.scope:ACCESS_TOKEN",
    "verified",
    ...Throttle::middleware("api-read"),
])->prefix("v1/admin/notifications")->group(function (): void {
    Route::get("/", [ NotificationAdminController::class, "index", ]);
    Route::get("/{id}", [ NotificationAdminController::class, "show", ]);
});

Route::middleware([
    "auth:api",
    "jwt.scope:ACCESS_TOKEN",
    "verified",
    ...Throttle::middleware("api-write"),
])->prefix("v1/admin/notifications")->group(function (): void {
    Route::delete("/deactivate/{id}", [ NotificationAdminController::class, "deactivate", ]);
    Route::delete("/activate/{id}", [ NotificationAdminController::class, "activate", ]);
});

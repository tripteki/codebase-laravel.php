<?php

use Modules\Notification\App\Http\Controllers\NotificationAdminController;
use Illuminate\Support\Facades\Route;

Route::middleware([ "auth:api", "jwt.scope:ACCESS_TOKEN", "verified", "throttle:api-read", ])->prefix("v1/admin/notifications")->group(function (): void {

    Route::get("/", [ NotificationAdminController::class, "index", ]);
    Route::delete("/deactivate/{id}", [ NotificationAdminController::class, "deactivate", ]);
    Route::delete("/activate/{id}", [ NotificationAdminController::class, "activate", ]);
    Route::get("/{id}", [ NotificationAdminController::class, "show", ]);
});

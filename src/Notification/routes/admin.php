<?php

use App\Support\Throttle;
use Illuminate\Support\Facades\Route;
use Modules\Notification\App\Http\Controllers\NotificationAdminController;

Route::middleware([...Throttle::middleware("api-read")])
    ->prefix("notifications")
    ->middleware("tenant.addon:modules_notification")
    ->group(function (): void {
        Route::get("/", [NotificationAdminController::class, "index"]);
        Route::get("/{id}", [NotificationAdminController::class, "show"]);
    });

Route::middleware([...Throttle::middleware("api-write")])
    ->prefix("notifications")
    ->middleware("tenant.addon:modules_notification")
    ->group(function (): void {
        Route::delete("/deactivate/{id}", [NotificationAdminController::class, "deactivate"]);
        Route::delete("/activate/{id}", [NotificationAdminController::class, "activate"]);
    });
